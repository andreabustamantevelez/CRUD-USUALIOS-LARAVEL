@php
    // =====================================================================
    // 1. COLORES SEGÚN EL ROL (alineado con theme.css bloque 10)
    // =====================================================================
    $isFamiliar = $usuario->rol === 'familiar';
    $isAdmin    = $usuario->rol === 'administrador';

    $colorMarca = '#059669';
    $iconBg     = '#059669';
    $iconBgSoft = '#a7f3d0';
    $iconFg     = '#ffffff';
    $textMain   = '#0f172a';
    $textAccent = '#059669';
    $textMuted  = '#64748b';

    $colorMarcaDark = '#6eddb2';
    $iconBgSoftDark = '#103127';

    if ($isFamiliar) {
        $colorMarca     = '#4648d4';
        $iconBg         = '#4648d4';
        $iconBgSoft     = '#c0c1ff';
        $textAccent     = '#4648d4';

        $colorMarcaDark = '#c1c2ff';
        $iconBgSoftDark = '#232370';
    } elseif ($isAdmin) {
        $colorMarca     = '#2563eb';
        $iconBg         = '#2563eb';
        $iconBgSoft     = '#bfdbfe';
        $textAccent     = '#2563eb';

        $colorMarcaDark = '#93b4ff';
        $iconBgSoftDark = '#1e3a8a';
    }

    // =====================================================================
    // 2. DATOS
    // =====================================================================
    $correo    = $correo ?? $usuario->email ?? '';
    $expiraEn  = $expiraEn ?? '30 minutos';
    $preheader = 'Restablece tu contraseña en Cuentas Claras.';

    // =====================================================================
    // 3. LOGO
    // =====================================================================
    $svgTemplate = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 48" fill="none">
        <rect x="2" y="6" width="36" height="36" rx="10" fill="%s"/>
        <path d="M12 24C12 18.4772 16.4772 14 22 14C25.5 14 28.5 15.8 30.2 18.5" stroke="%s" stroke-width="3" stroke-linecap="round"/>
        <path d="M28 24C28 29.5228 23.5228 34 18 34C14.5 34 11.5 32.2 9.8 29.5" stroke="%s" stroke-width="3" stroke-linecap="round"/>
        <circle cx="20" cy="24" r="3" fill="%s"/>
        <text x="48" y="27" font-family="Helvetica, Arial, sans-serif" font-size="19" font-weight="700" fill="%s">Cuentas<tspan fill="%s">Claras</tspan></text>
        <text x="48" y="38" font-family="Helvetica, Arial, sans-serif" font-size="9" font-weight="600" fill="%s" letter-spacing="1">FINANZAS PERSONALES</text>
    </svg>';

    $svgClaro = sprintf($svgTemplate, $iconBg, $iconFg, $iconBgSoft, $iconFg, $textMain, $textAccent, $textMuted);
    $logoClaroUrl = $message->embedData($svgClaro, 'logo-claro.svg', 'image/svg+xml');

    $textMainDark  = '#e3e6ea';
    $textMutedDark = '#b8bfc7';
    $svgOscuro = sprintf($svgTemplate, $iconBg, $iconFg, $iconBgSoft, $iconFg, $textMainDark, $colorMarcaDark, $textMutedDark);
    $logoOscuroUrl = $message->embedData($svgOscuro, 'logo-oscuro.svg', 'image/svg+xml');

    // =====================================================================
    // 4. HERO: candado + check (56x56)
    // =====================================================================
    $heroTemplate = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 56 56" fill="none">
        <rect width="56" height="56" rx="14" fill="%s"/>
        <rect x="17" y="22" width="22" height="18" rx="3" fill="none" stroke="%s" stroke-width="2"/>
        <path d="M22 22V18a6 6 0 0 1 12 0v4" fill="none" stroke="%s" stroke-width="2" stroke-linecap="round"/>
        <circle cx="28" cy="30" r="1.8" fill="%s"/>
        <rect x="27.2" y="31" width="1.6" height="4" rx="0.8" fill="%s"/>
        <circle cx="39" cy="39" r="8" fill="%s"/>
        <path d="M35.5 39 L38.5 42 L43.5 36.5" stroke="%s" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>';

    $heroClaroSvg  = sprintf($heroTemplate, $iconBgSoft, $iconBg, $iconBg, $iconBg, $iconBg, $iconBg, $iconFg);
    $heroClaroUrl  = $message->embedData($heroClaroSvg, 'hero-claro.svg', 'image/svg+xml');

    $heroOscuroSvg = sprintf($heroTemplate, $iconBgSoftDark, $colorMarcaDark, $colorMarcaDark, $colorMarcaDark, $colorMarcaDark, $colorMarcaDark, '#0f1216');
    $heroOscuroUrl = $message->embedData($heroOscuroSvg, 'hero-oscuro.svg', 'image/svg+xml');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>Restablece tu contraseña — Cuentas Claras</title>
    <style>
        @media (prefers-color-scheme: dark) {
            .email-body          { background-color: #0f1216 !important; }
            .email-card          { background-color: #14181d !important; border-color: #24292f !important; }
            .email-text-main     { color: #e3e6ea !important; }
            .email-text-muted    { color: #b8bfc7 !important; }
            .email-footer        { background-color: #0a0d10 !important; border-color: #24292f !important; }
            .email-divider       { border-color: #24292f !important; }
            .email-highlight     { background-color: #181d23 !important; border-color: #24292f !important; }
            .email-fallback      { background-color: #0a0d10 !important; border-color: #343b42 !important; }
            .email-fallback-text { color: #b8bfc7 !important; }
            .email-fallback-link { color: {{ $colorMarcaDark }} !important; }

            .logo-claro { display: none !important; }
            .logo-oscuro { display: block !important; }
            .hero-claro { display: none !important; }
            .hero-oscuro { display: block !important; }
        }
    </style>
</head>
<body class="email-body" style="margin:0; padding:0; background-color:#f8f9ff; font-family:'Helvetica Neue',Helvetica,Arial,sans-serif; -webkit-font-smoothing:antialiased;">

    <div style="display:none; font-size:1px; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden; mso-hide:all;">
        {{ $preheader }}
    </div>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent; padding:40px 20px;">
        <tr>
            <td align="center">
                <table class="email-card" width="100%" cellpadding="0" cellspacing="0" border="0"
                       style="max-width:600px; background-color:#ffffff; border-radius:20px; overflow:hidden;
                              box-shadow:0 8px 24px rgba(15,23,42,0.08); border:1px solid #e2e8f0;">

                    <tr>
                        <td style="height:6px; background-color:{{ $colorMarca }}; line-height:6px; font-size:1px;">&nbsp;</td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:32px 20px 24px 20px;">
                            <img src="{{ $logoClaroUrl }}" alt="Cuentas Claras" class="logo-claro"
                                 style="height:48px; width:auto; display:block; margin:0 auto;">
                            <img src="{{ $logoOscuroUrl }}" alt="Cuentas Claras" class="logo-oscuro"
                                 style="height:48px; width:auto; display:none; margin:0 auto;">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 40px 32px 40px;">

                            <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto 20px auto;">
                                <tr>
                                    <td align="center">
                                        <img src="{{ $heroClaroUrl }}" alt="" class="hero-claro" width="56" height="56"
                                             style="display:block; width:56px; height:56px; margin:0 auto;">
                                        <img src="{{ $heroOscuroUrl }}" alt="" class="hero-oscuro" width="56" height="56"
                                             style="display:none; width:56px; height:56px; margin:0 auto;">
                                    </td>
                                </tr>
                            </table>

                            <h1 class="email-text-main"
                                style="margin:0 0 12px 0; font-size:24px; font-weight:700; line-height:1.3;
                                       color:{{ $textMain }}; text-align:center; letter-spacing:-0.01em;">
                                Restablece tu contraseña
                            </h1>

                            <p class="email-text-main"
                               style="margin:0 0 24px 0; font-size:15px; line-height:1.6; color:#3d4a42; text-align:center;">
                                Hola, <strong style="color:{{ $textMain }};">{{ $usuario->nombre }}</strong>.
                                Recibimos una solicitud para restablecer la contraseña de tu cuenta.
                            </p>

                            @if(!empty($correo))
                                <table class="email-highlight" cellpadding="0" cellspacing="0" border="0" width="100%"
                                       style="background-color:#f8f9ff; border:1px solid #e2e8f0; border-radius:12px; margin:0 0 28px 0;">
                                    <tr>
                                        <td align="center" style="padding:14px 20px;">
                                            <p class="email-text-muted"
                                               style="margin:0 0 4px 0; font-size:11px; font-weight:600;
                                                      letter-spacing:0.5px; text-transform:uppercase; color:{{ $textMuted }};">
                                                Cuenta
                                            </p>
                                            <p class="email-text-main"
                                               style="margin:0; font-size:15px; font-weight:600; color:{{ $textMain }}; word-break:break-all;">
                                                {{ $correo }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto 12px auto;">
                                <tr>
                                    <td align="center" style="border-radius:10px; background-color:{{ $colorMarca }};">
                                        <a href="{{ $url }}"
                                           style="display:inline-block; padding:14px 36px; background-color:{{ $colorMarca }};
                                                  color:#ffffff; text-decoration:none; border-radius:10px; font-weight:600;
                                                  font-size:16px; font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;
                                                  box-shadow:0 4px 12px -2px rgba(0,0,0,0.15);">
                                            Restablecer contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p class="email-text-muted"
                               style="margin:0 0 28px 0; font-size:13px; color:{{ $textMuted }}; text-align:center;">
                                Este enlace vence en {{ $expiraEn }}.
                            </p>

                            <table class="email-fallback" cellpadding="0" cellspacing="0" border="0" width="100%"
                                   style="background-color:#f8f9ff; border:1px dashed #cbd5e1; border-radius:10px; margin:0 0 24px 0;">
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <p class="email-fallback-text"
                                           style="margin:0 0 6px 0; font-size:12px; color:{{ $textMuted }};">
                                            Si el botón no funciona, copia y pega este enlace en tu navegador:
                                        </p>
                                        <p class="email-fallback-link"
                                           style="margin:0; font-size:12px; line-height:1.5; word-break:break-all;
                                                  color:{{ $colorMarca }}; font-weight:500;">
                                            {{ $url }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:0 0 20px 0;">
                                <tr>
                                    <td class="email-divider" style="border-top:1px solid #e2e8f0; font-size:1px; line-height:1px;">&nbsp;</td>
                                </tr>
                            </table>

                            <p class="email-text-muted"
                               style="margin:0; font-size:13px; line-height:1.6; color:{{ $textMuted }}; text-align:center;">
                                <strong style="color:#3d4a42;">¿No fuiste tú?</strong>
                                Ignora este mensaje. Tu contraseña no cambia y tu cuenta sigue segura.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td class="email-footer" align="center"
                            style="padding:24px 40px; background-color:#f8f9ff; border-top:1px solid #e2e8f0;
                                   color:{{ $textMuted }}; font-size:12px;">
                            <p class="email-text-muted" style="margin:0 0 6px 0; font-size:12px;">
                                Este es un correo automático, por favor no respondas.
                            </p>
                            <p class="email-text-muted" style="margin:0; font-size:12px;">
                                &copy; {{ date('Y') }} Cuentas Claras. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>
                </table>

                <p class="email-text-muted"
                   style="margin:20px auto 0 auto; max-width:500px; font-size:11px; line-height:1.5;
                          color:#94a3b8; text-align:center;">
                    Recibes este correo porque alguien solicitó restablecer la contraseña de una cuenta de
                    Cuentas Claras. Si no fuiste tú, no hagas clic en el enlace.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>