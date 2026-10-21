@extends('layouts.app')

@section('titulo', 'Ajustes · Perfil · Cuentas Claras')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ajustes.css') }}">
@endpush

@section('content')
    @php($esAdmin = $usuario->rol === 'administrador')
    <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
        <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">
            <header>
                <h1 class="font-headline-xl text-headline-xl text-on-surface">
                    Ajustes · Perfil
                </h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                    @if($esAdmin)
                        Actualiza tus datos personales y tu contraseña.
                    @else
                        Actualiza tus datos personales, tu ingreso mensual y tu contraseña.
                    @endif
                </p>
            </header>

            @include('app.ajustes._tabs')

            {{-- Aviso: Cambio de correo pendiente --}}
            @if ($usuario->pending_email)
                <div class="ajustes-aviso" role="alert">
                    <span class="material-symbols-outlined text-[20px]">forward_to_inbox</span>
                    <div>
                        <strong>Tienes un cambio de correo pendiente.</strong>
                        Hemos enviado un enlace de confirmación a
                        <strong>{{ $usuario->pending_email }}</strong>.
                        Revisa tu bandeja de entrada (o la carpeta de spam) para verificar el
                        nuevo correo. Tu correo actual seguirá siendo
                        <strong>{{ $usuario->email }}</strong> hasta que confirmes el cambio.

                        <form method="POST" action="{{ route('ajustes.confirmar-correo.reenviar') }}" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-secondary">
                                Reenviar correo de confirmación
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Mensajes globales --}}
            @if (session('exito'))
                <div class="ajustes-exito" role="status">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    <span>{{ session('exito') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="ajustes-error" role="alert">
                    <span class="ajustes-error__titulo">Revisa los siguientes datos:</span>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="card p-space-lg">
                <form method="POST" action="{{ route('ajustes.perfil.update') }}" class="ajustes-form">
                    @csrf
                    @method('PUT')

                    {{-- Datos del perfil --}}
                    <div class="ajustes-seccion-header">
                        <h2 class="ajustes-seccion-titulo">Datos del perfil</h2>
                        <p class="ajustes-seccion-desc">
                            Tu nombre y correo aparecen en toda la aplicación.
                            Si cambias el correo, te pediremos tu contraseña actual y te
                            enviaremos un enlace de confirmación.
                        </p>
                    </div>

                    <div class="ajustes-form__grid ajustes-form__grid--2">
                        <div class="ajustes-campo">
                            <label for="nombre" class="ajustes-campo__label">Nombre completo</label>
                            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $usuario->nombre) }}"
                                maxlength="100" required autocomplete="name" class="ajustes-campo__input">
                            @error('nombre')
                                <p class="ajustes-campo__error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ajustes-campo">
                            <label for="email" class="ajustes-campo__label">Correo electrónico</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}"
                                maxlength="150" required autocomplete="email" class="ajustes-campo__input">
                            @error('email')
                                <p class="ajustes-campo__error">{{ $message }}</p>
                            @enderror
                        </div>

                        @if(!$esAdmin)
                            <div class="ajustes-campo ajustes-campo--full">
                                <label for="ingreso_mensual" class="ajustes-campo__label">
                                    Ingreso mensual
                                </label>
                                <div class="ajustes-campo__sufijo">
                                    <input type="text" inputmode="numeric" id="ingreso_mensual" name="ingreso_mensual"
                                        value="{{ old('ingreso_mensual', $usuario->ingreso_mensual !== null ? number_format($usuario->ingreso_mensual, 0, ',', '.') : '') }}"
                                        autocomplete="off" placeholder="Ej: 3.500.000" class="ajustes-campo__input">
                                    <span class="ajustes-campo__sufijo-texto">COP</span>
                                </div>
                                <p class="ajustes-campo__hint">
                                    Se usa en el simulador de créditos y en el informe mensual.
                                    Déjalo vacío si aún no lo tienes.
                                </p>
                                @error('ingreso_mensual')
                                    <p class="ajustes-campo__error">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>

                    <hr class="ajustes-separador">

                    {{-- Cambiar contraseña --}}
                    <div class="ajustes-seccion-header">
                        <h2 class="ajustes-seccion-titulo">Cambiar contraseña</h2>
                        <p class="ajustes-seccion-desc">
                            Si no deseas cambiarla, deja estos tres campos vacíos.
                            Si vas a cambiar el correo, también necesitamos tu contraseña actual
                            como medida de seguridad.
                        </p>
                    </div>

                    <div class="ajustes-form__grid">
                        <div class="ajustes-campo">
                            <label for="contrasena_actual" class="ajustes-campo__label">
                                Contraseña actual
                            </label>
                            <input type="password" id="contrasena_actual" name="contrasena_actual"
                                autocomplete="current-password" class="ajustes-campo__input">
                            @error('contrasena_actual')
                                <p class="ajustes-campo__error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ajustes-campo">
                            <label for="nueva_contrasena" class="ajustes-campo__label">
                                Nueva contraseña
                            </label>
                            <input type="password" id="nueva_contrasena" name="nueva_contrasena" autocomplete="new-password"
                                class="ajustes-campo__input">
                            <p class="ajustes-campo__hint">Mínimo 8 caracteres.</p>
                            @error('nueva_contrasena')
                                <p class="ajustes-campo__error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ajustes-campo">
                            <label for="nueva_contrasena_confirmation" class="ajustes-campo__label">
                                Confirmar nueva contraseña
                            </label>
                            <input type="password" id="nueva_contrasena_confirmation" name="nueva_contrasena_confirmation"
                                autocomplete="new-password" class="ajustes-campo__input">
                        </div>
                    </div>

                    <div class="ajustes-acciones">
                        <button type="submit" class="btn btn-primary">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </main>

    @push('scripts')
        <script>
            (function () {
                var ingreso = document.getElementById('ingreso_mensual');
                if (!ingreso) return;
                ingreso.addEventListener('input', function () {
                    var valor = this.value.replace(/\D/g, '');
                    this.value = valor.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                });
            })();
        </script>
    @endpush
@endsection
