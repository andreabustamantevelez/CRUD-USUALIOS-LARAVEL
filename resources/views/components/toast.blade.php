{{--
  ==============================================================================
  ARCHIVO : resources/views/components/toast.blade.php
  QUÉ ES  : Mensaje flash de éxito. Uso: <x-toast /> (ya incluido en layouts/base).
  QUÉ HAY QUE HACER (en orden):
    1. En los controladores: return redirect()->route(...)->with('exito', 'Gasto guardado').
    2. Usar los textos de la especificación (sección 10).
  CÓMO HACERLO / NOTAS:
    - Desaparece solo a los 3,5 s (public/js/shell.js). Los errores de formulario NO van aquí: van
      bajo cada campo con @error.
  ==============================================================================
--}}

{{-- Mensajes de éxito (flash): en el controlador -> redirect()->with('exito', 'Gasto guardado') --}}
@if(session('exito'))
  <div
    id="toastFlash"
    class="fixed bottom-6 right-6 z-[70] bg-inverse-surface text-inverse-on-surface px-space-md py-3 rounded-lg shadow-xl flex items-center gap-2 transition-opacity duration-300 font-label-md text-label-md"
  >
    <span class="material-symbols-outlined text-[20px] text-primary-fixed-dim">check_circle</span>
    <span>{{ session('exito') }}</span>
  </div>
@endif
