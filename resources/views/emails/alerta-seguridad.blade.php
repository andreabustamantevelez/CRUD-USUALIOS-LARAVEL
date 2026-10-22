@php
$config = [
    'password.changed' => [
        'icono'   => 'lock_reset',
        'titulo'  => 'Tu contraseña fue cambiada',
        'mensaje' => 'La contraseña de tu cuenta de Cuentas Claras se cambió correctamente. Si fuiste tú, no necesitas hacer nada.',
    ],
    'email.changed' => [
        'icono'   => 'mail',
        'titulo'  => 'Tu correo cambió',
        'mensaje' => 'El correo asociado a tu cuenta de Cuentas Claras se actualizó. Este aviso llega a tu correo ANTERIOR como medida de seguridad.',
    ],
    'account.suspended' => [
        'icono'   => 'block',
        'titulo'  => 'Tu cuenta fue suspendida',
        'mensaje' => 'Un administrador suspendió tu cuenta de Cuentas Claras. No podrás iniciar sesión hasta que se reactive.',
    ],
    'login.new_ip' => [
        'icono'   => 'login',
        'titulo'  => 'Nuevo inicio de sesión',
        'mensaje' => 'Detectamos un inicio de sesión desde una IP nueva en tu cuenta de Cuentas Claras.',
    ],
    'login.blocked' => [
        'icono'   => 'security',
        'titulo'  => 'Intentos de inicio de sesión bloqueados',
        'mensaje' => 'Detectamos varios intentos fallidos de inicio de sesión en tu cuenta. Por seguridad, el acceso quedó bloqueado temporalmente.',
    ],
];
$info = $config[$evento] ?? [
    'icono'   => 'shield',
    'titulo'  => 'Alerta de seguridad',
    'mensaje' => 'Ocurrió un evento de seguridad en tu cuenta de Cuentas Claras.',
];

$etiquetas = [
    'fecha'        => 'Fecha',
    'ip'           => 'Dirección IP',
    'user_agent'   => 'Navegador',
    'correo_nuevo' => 'Nuevo correo',
    'intentos'     => 'Intentos fallidos',
];
@endphp
<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="color-scheme" content="light dark">
  <meta name="supported-color-schemes" content="light dark">
  <title>{{ $info['titulo'] }} — Cuentas Claras</title>
  <!--[if mso]>
  <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
  <![endif]-->
  <style>
    body { margin: 0; padding: 0; background: #f8f9ff; font-family: 'Plus Jakarta Sans', Arial, sans-serif; color: #0b1c30; }
    .wrapper { width: 100%; padding: 32px 16px; }
    .card { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(15,23,42,0.06); }
    .header { padding: 24px 32px; border-bottom: 1px solid #e2e8f0; }
    .body { padding: 32px; }
    .icon { display: inline-flex; width: 48px; height: 48px; border-radius: 12px; background: #ecfdf5; color: #059669; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 16px; }
    h1 { margin: 0 0 16px; font-size: 22px; line-height: 1.3; font-weight: 700; color: #0b1c30; }
    p { margin: 0 0 16px; font-size: 15px; line-height: 1.6; color: #3d4a42; }
    .detalles { width: 100%; background: #f3f4f6; border-radius: 8px; margin: 16px 0; border-collapse: collapse; }
    .detalles td { padding: 8px 12px; font-size: 13px; }
    .detalles td:first-child { color: #6b7280; width: 40%; }
    .detalles td:last-child { color: #111827; font-weight: 600; word-break: break-all; }
    .nota { margin-top: 24px; padding: 16px; background: #fef3c7; border-radius: 8px; border-left: 4px solid #d97706; font-size: 13px; line-height: 1.5; color: #78350f; }
    .footer { padding: 24px 32px; background: #f8f9ff; border-top: 1px solid #e2e8f0; font-size: 12px; line-height: 1.5; color: #6b7280; text-align: center; }
    @media (prefers-color-scheme: dark) {
      body { background: #0f1216; color: #e3e6ea; }
      .card { background: #14181d; }
      .header { border-color: #24292f; }
      h1 { color: #e3e6ea; }
      p { color: #b8bfc7; }
      .detalles { background: #1f252c; }
      .detalles td:last-child { color: #e3e6ea; }
      .nota { background: #3a2c0f; color: #f2c067; }
      .footer { background: #0a0d10; border-color: #24292f; color: #98a0a8; }
    }
  </style>
</head>
<body>
  {{-- Preheader (invisible, aparece en la bandeja) --}}
  <div style="display:none;max-height:0;overflow:hidden;mso-hide:all;">{{ $info['mensaje'] }}</div>

  <div class="wrapper">
    <div class="card">

      <div class="header">
        <strong style="font-size: 16px; color: #0b1c30;">Cuentas Claras</strong>
      </div>

      <div class="body">
        <div class="icon" aria-hidden="true">{{ $info['icono'] }}</div>
        <h1>{{ $info['titulo'] }}</h1>
        <p>Hola{{ $nombre ? ' ' . $nombre : '' }},</p>
        <p>{{ $info['mensaje'] }}</p>

        @if (!empty($detalles))
          <table class="detalles" cellpadding="0" cellspacing="0" role="presentation">
            @foreach ($detalles as $clave => $valor)
              @if (!is_null($valor) && $valor !== '')
                <tr>
                  <td>{{ $etiquetas[$clave] ?? ucfirst(str_replace('_', ' ', $clave)) }}</td>
                  <td>{{ $valor }}</td>
                </tr>
              @endif
            @endforeach
          </table>
        @endif

        <div class="nota">
          <strong>Si no fuiste tú:</strong> cambia tu contraseña de inmediato y revisa los accesos recientes desde Ajustes → Perfil.
        </div>
      </div>

      <div class="footer">
        Este correo se envió automáticamente. No respondas a este mensaje.<br>
        Cuentas Claras · <a href="{{ route('inicio') }}" style="color: #059669; text-decoration: none;">cuentasclaras.app</a>
      </div>

    </div>
  </div>
</body>
</html>