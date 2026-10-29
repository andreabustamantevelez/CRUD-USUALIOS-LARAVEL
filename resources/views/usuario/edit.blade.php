<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card shadow-lg border-0 rounded-3">

                    <!-- Encabezado -->
                    <div class="card-header bg-primary text-white py-3">
                        <h3 class="text-center mb-0">Editar Usuario</h3>
                    </div>

                    <!-- Formulario -->
                    <div class="card-body p-4">

                        <!-- Mensajes de error -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Por favor, revisa los siguientes errores:</strong>

                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form
                            method="POST"
                            action="{{ route('usuarios.update', $usuario->id_usuario) }}"
                        >
                            @csrf
                            @method('PUT')

                            <!-- Nombre de usuario -->
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    Nombre de usuario
                                </label>

                                <input
                                    class="form-control"
                                    id="username"
                                    name="username"
                                    type="text"
                                    value="{{ old('username', $usuario->username) }}"
                                    required
                                >
                            </div>

                            <!-- Contraseña -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    Nueva contraseña
                                </label>

                                <input
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    type="password"
                                    minlength="6"
                                >

                                <div class="form-text">
                                    Déjala vacía si no deseas cambiar la contraseña actual.
                                </div>
                            </div>

                            <!-- Rol -->
                            <div class="mb-3">
                                <label for="rol" class="form-label">Rol</label>

                                <select
                                    class="form-select"
                                    id="rol"
                                    name="rol"
                                    required
                                >
                                    <option
                                        value="admin"
                                        {{ old('rol', $usuario->rol) == 'admin' ? 'selected' : '' }}
                                    >
                                        Administrador
                                    </option>

                                    <option
                                        value="vendedor"
                                        {{ old('rol', $usuario->rol) == 'vendedor' ? 'selected' : '' }}
                                    >
                                        Vendedor
                                    </option>

                                    <option
                                        value="supervisor"
                                        {{ old('rol', $usuario->rol) == 'supervisor' ? 'selected' : '' }}
                                    >
                                        Supervisor
                                    </option>
                                </select>
                            </div>

                            <!-- Estado -->
                            <div class="mb-3">
                                <label for="activo" class="form-label">Estado</label>

                                <select
                                    class="form-select"
                                    id="activo"
                                    name="activo"
                                    required
                                >
                                    <option
                                        value="1"
                                        {{ old('activo', $usuario->activo) == '1' ? 'selected' : '' }}
                                    >
                                        Activo
                                    </option>

                                    <option
                                        value="0"
                                        {{ old('activo', $usuario->activo) == '0' ? 'selected' : '' }}
                                    >
                                        Inactivo
                                    </option>
                                </select>
                            </div>

                            <!-- Fecha de creación -->
                            <div class="mb-3">
                                <label for="fecha_creacion" class="form-label">
                                    Fecha de creación
                                </label>

                                <input
                                    class="form-control"
                                    id="fecha_creacion"
                                    name="fecha_creacion"
                                    type="date"
                                    value="{{ old('fecha_creacion', $usuario->fecha_creacion) }}"
                                    required
                                >
                            </div>

                            <!-- Empresa -->
                            <div class="mb-4">
                                <label for="id_empresa" class="form-label">
                                    Empresa
                                </label>

                                <select
                                    class="form-select"
                                    id="id_empresa"
                                    name="id_empresa"
                                    required
                                >
                                    <option value="">Seleccione una empresa</option>

                                    @foreach ($empresas as $empresa)
                                        <option
                                            value="{{ $empresa->id_empresa }}"
                                            {{ old('id_empresa', $usuario->id_empresa) == $empresa->id_empresa ? 'selected' : '' }}
                                        >
                                            {{ $empresa->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Botones -->
                            <div class="d-grid gap-2">
                                <button
                                    class="btn btn-primary btn-lg"
                                    type="submit"
                                >
                                    Actualizar usuario
                                </button>

                                <a
                                    class="btn btn-secondary"
                                    href="{{ route('usuarios.index') }}"
                                >
                                    Cancelar y volver
                                </a>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
