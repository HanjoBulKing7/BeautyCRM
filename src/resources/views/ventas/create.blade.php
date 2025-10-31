@extends('layouts.app')

@section('title', 'Crear venta')

@section('content')
<div class="bg-white p-6 rounded-lg shadow dark:bg-gray-800">
  <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Crear venta</h1>
  
  <!-- Mostrar mensajes de error -->
  @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 dark:bg-red-900 dark:border-red-700 dark:text-red-200 px-4 py-3 rounded mb-4">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  
  <!-- Mostrar mensajes de sesión -->
  @if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 dark:bg-red-900 dark:border-red-700 dark:text-red-200 px-4 py-3 rounded mb-4">
      {{ session('error') }}
    </div>
  @endif
  
  <!-- Formulario principal -->
  <form method="POST" action="{{ route('ventas.store') }}" class="space-y-4" id="venta-form">
    @include('ventas._form', ['venta' => $venta])
  </form>
</div>

<!-- Iframe oculto para impresión del ticket -->
<iframe id="print-frame" style="display:none; width:0; height:0; border:0;"></iframe>

<!-- Script de envío + impresión -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('venta-form');
  const printFrame = document.getElementById('print-frame');
  const btnGuardar = document.getElementById('btn-guardar');

  function serializeVentaForm() {
    const payload = {
      cliente_id: document.getElementById('cliente_select')?.value || 0,
      fecha: document.querySelector('input[name="fecha"]')?.value,
      subtotal: parseFloat(document.getElementById('subtotal')?.value || 0),
      descuento: parseFloat(document.querySelector('input[name="descuento"]')?.value || 0),
      impuestos: parseFloat(document.querySelector('input[name="impuestos"]')?.value || 0),
      total: parseFloat(document.getElementById('total')?.value || 0),
      metodo_pago: 'efectivo',
      referencia_pago: null,
      destinatario_transferencia: null,
      notas: document.querySelector('textarea[name="notas"]')?.value || '',
      productos: [],
      pagos: []
    };

    // Productos
    document.querySelectorAll('.producto-row').forEach(row => {
      const producto_id = row.querySelector('.producto-select')?.value;
      const cantidad = parseFloat(row.querySelector('.cantidad-input')?.value || 0);
      const precio_unitario = parseFloat(row.querySelector('.precio-input')?.value || 0);
      const total_linea = parseFloat(row.querySelector('.total-linea-input')?.value || (cantidad * precio_unitario) || 0);

      if (producto_id) {
        payload.productos.push({
          producto_id: Number(producto_id),
          cantidad,
          precio_unitario,
          total_linea
        });
      }
    });

    // Pagos
    document.querySelectorAll('.pago-row').forEach(row => {
      const metodo = row.querySelector('.pago-metodo')?.value || '';
      const monto  = parseFloat(row.querySelector('.pago-monto')?.value || 0);
      const referencia = row.querySelector('.pago-referencia')?.value || null;
      const destinatario = row.querySelector('.pago-destinatario')?.value || null;

      if (metodo && monto > 0) {
        payload.pagos.push({
          metodo,
          monto,
          referencia,
          destinatario
        });
      }
    });

    // Método principal
    if (payload.pagos.length > 1) {
      payload.metodo_pago = 'multipago';
    } else if (payload.pagos.length === 1) {
      payload.metodo_pago = payload.pagos[0].metodo;
      if (payload.metodo_pago === 'transferencia') {
        payload.destinatario_transferencia = payload.pagos[0].destinatario || null;
      }
    }

    if (payload.metodo_pago === 'transferencia' && !payload.destinatario_transferencia) {
      const firstTransfer = payload.pagos.find(p => p.metodo === 'transferencia');
      payload.destinatario_transferencia = firstTransfer?.destinatario || null;
    }

    return payload;
  }

  function validar(payload) {
    if (!payload.fecha) return 'La fecha es requerida.';
    if (!payload.productos.length) return 'Debe agregar al menos un producto.';
    if (payload.productos.some(p => !p.producto_id || p.cantidad <= 0 || p.precio_unitario < 0)) {
      return 'Verifica producto, cantidad y precio en cada línea.';
    }
    if (!payload.pagos.length) return 'Debe agregar al menos un pago.';
    const sumaPagos = payload.pagos.reduce((a, p) => a + (p.monto || 0), 0);
    if (Math.abs(sumaPagos - (payload.total || 0)) > 0.01) {
      return `La suma de los pagos ($${sumaPagos.toFixed(2)}) debe ser igual al total ($${(payload.total||0).toFixed(2)}).`;
    }
    if (payload.metodo_pago === 'transferencia' && !payload.destinatario_transferencia) {
      return 'Para transferencia, selecciona el destinatario.';
    }
    return null;
  }

  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    // Bloquear botón para evitar múltiples clics
    btnGuardar.disabled = true;
    btnGuardar.textContent = 'Guardando...';

    const payload = serializeVentaForm();
    const errorMsg = validar(payload);
    if (errorMsg) {
      alert(errorMsg);
      btnGuardar.disabled = false;
      btnGuardar.textContent = 'Guardar';
      return;
    }

    try {
      const res = await fetch("{{ route('ventas.store') }}", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify(payload)
      });

      const data = await res.json().catch(() => ({}));

      if (data?.success && data?.redirect_url) {
        // ✅ Imprimir cuando el PDF cargue
        printFrame.onload = function () {
          setTimeout(() => {
            try {
              printFrame.contentWindow?.focus();
              printFrame.contentWindow?.print();

              // Esperar a que el usuario cierre la ventana de impresión
              const checkInterval = setInterval(() => {
                // Cuando la ventana de impresión desaparece (focus vuelve a la página)
                if (document.hasFocus()) {
                  clearInterval(checkInterval);
                  window.location.href = "{{ route('ventas.index') }}";
                }
              }, 1000);
            } catch (err) {
              console.warn('No se pudo ejecutar print() en el iframe:', err);
              btnGuardar.disabled = false;
              btnGuardar.textContent = 'Guardar';
            }
          }, 700);
        };
        printFrame.src = data.redirect_url;
      } else {
        alert(data?.message || 'No se recibió la URL del ticket. Revisa validaciones.');
        btnGuardar.disabled = false;
        btnGuardar.textContent = 'Guardar';
      }
    } catch (err) {
      console.error(err);
      alert('Ocurrió un error al crear la venta.');
      btnGuardar.disabled = false;
      btnGuardar.textContent = 'Guardar';
    }
  });
});
</script>
@endsection
