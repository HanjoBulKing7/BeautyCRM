@auth
@php
  $bbCliente = \App\Models\Cliente::where('user_id', auth()->id())->first();
@endphp
@if($bbCliente)
<div style="border:1.5px solid #C8A64A;border-radius:16px;background:linear-gradient(135deg,#fffbf2,#ffffff);padding:18px 20px;margin:14px 0;display:flex;gap:14px;align-items:flex-start;flex-wrap:wrap;">
  <div style="font-size:28px;line-height:1;">🎂</div>
  <div style="flex:1;min-width:220px;">
    @if($bbCliente->fecha_nacimiento)
      <p style="margin:0;font-weight:800;color:#0f172a;">Tu cumpleaños está registrado</p>
      <p style="margin:4px 0 0;color:#64748b;font-size:14px;">
        {{ $bbCliente->fecha_nacimiento->translatedFormat('d \d\e F') }} · Te enviaremos un regalo en tu mes 🎁
      </p>
      <p style="margin:6px 0 0;color:#94a3b8;font-size:12px;">
        <i class="fas fa-lock" style="margin-right:4px;"></i>Este dato no se puede modificar. Si hay un error, escríbenos a contacto@beautybonita.com.mx
      </p>
    @else
      <p style="margin:0;font-weight:800;color:#0f172a;">Recibe un regalo en tu cumpleaños</p>
      <p style="margin:4px 0 10px;color:#64748b;font-size:14px;">
        Registra tu fecha de nacimiento y te enviaremos un descuento especial. <strong>Solo puedes registrarla una vez.</strong>
      </p>
      <form method="POST" action="{{ route('cliente.cumpleanos.store') }}"
            style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;"
            onsubmit="return confirm('¿Confirmas tu fecha de nacimiento? No podrás cambiarla después.');">
        @csrf
        <input type="date" name="fecha_nacimiento" required
               max="{{ now()->subYears(10)->toDateString() }}" min="1920-01-02"
               value="{{ old('fecha_nacimiento') }}"
               style="border:1px solid #e2c87a;border-radius:10px;padding:8px 12px;font-size:14px;background:#fff;color:#0f172a;">
        <button type="submit"
                style="background:linear-gradient(135deg,#C8A64A,#8e6708);color:#fff;border:0;border-radius:999px;padding:9px 20px;font-weight:700;font-size:14px;cursor:pointer;">
          Guardar cumpleaños
        </button>
      </form>
      @error('fecha_nacimiento')
        <p style="margin:6px 0 0;color:#b91c1c;font-size:13px;">{{ $message }}</p>
      @enderror
    @endif

    @if(session('cumple_ok'))
      <p style="margin:8px 0 0;color:#15803d;font-size:13px;font-weight:600;">{{ session('cumple_ok') }}</p>
    @endif
    @if(session('cumple_error'))
      <p style="margin:8px 0 0;color:#b91c1c;font-size:13px;font-weight:600;">{{ session('cumple_error') }}</p>
    @endif
  </div>
</div>
@endif
@endauth
