@extends('layouts.app')

@section('page-title', 'Gestión de Empleados')
@section('title', 'Empleados - Salón de Belleza')

@section('content')
  {{-- ✅ Listado (SOLO HTML de la tabla/cards) --}}
  @include('admin.empleados.partials.index-content')

  {{-- ✅ Modal contenedor (UNA sola vez) --}}
  <div id="bbModal" class="fixed inset-0 z-[9999] hidden" aria-hidden="true">
      <div class="absolute inset-0 bg-black/40" data-bb-close></div>

      <div class="relative min-h-full flex items-center justify-center p-4">
          <div class="w-full max-w-3xl rounded-2xl bg-white shadow-xl overflow-hidden"
               role="dialog" aria-modal="true" aria-labelledby="bbModalTitle">

              <div class="flex items-center justify-between px-5 py-4 border-b">
                  <h2 id="bbModalTitle" class="font-semibold text-gray-900">Modal</h2>

                  <button type="button"
                          class="h-9 w-9 rounded-xl hover:bg-gray-100"
                          data-bb-close
                          aria-label="Cerrar">✕</button>
              </div>

              <div id="bbModalBody" class="p-5">
                  <div class="text-sm text-gray-500">Cargando...</div>
              </div>

          </div>
      </div>
  </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('bbModal');
  const titleEl = document.getElementById('bbModalTitle');
  const bodyEl = document.getElementById('bbModalBody');

  let lastFocus = null;

  function openModal({ title, url }) {
    lastFocus = document.activeElement;

    titleEl.textContent = title || 'Formulario';
    bodyEl.innerHTML = '<div class="text-sm text-gray-500">Cargando...</div>';

    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.text())
      .then(html => {
        bodyEl.innerHTML = html;

        const first = bodyEl.querySelector('input, select, textarea, button');
        if (first) first.focus();
      })
      .catch(() => {
        bodyEl.innerHTML = '<div class="text-sm text-red-600">No se pudo cargar el formulario.</div>';
      });
  }

  function closeModal() {
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    bodyEl.innerHTML = '';
    if (lastFocus) lastFocus.focus();
  }

  // Abrir modal desde cualquier botón/link con data-bb-modal
  document.addEventListener('click', (e) => {
    const a = e.target.closest('[data-bb-modal]');
    if (!a) return;

    e.preventDefault();

    openModal({
      title: a.getAttribute('data-bb-title'),
      url: a.getAttribute('data-bb-url') || a.getAttribute('href'),
    });
  });

  // Cerrar al hacer click en overlay o botones con data-bb-close
  modal.addEventListener('click', (e) => {
    if (e.target.matches('[data-bb-close]')) closeModal();
  });

  // Cerrar con ESC
  document.addEventListener('keydown', (e) => {
    if (!modal.classList.contains('hidden') && e.key === 'Escape') closeModal();
  });
});
</script>
@endpush
