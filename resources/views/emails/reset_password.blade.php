<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Dios Padre Hotel</title>
    <style type="text/css">
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f5f5f5; }
        table { border-collapse: collapse; }
        img { border: 0; outline: 0; }
    </style>
</head>
<body style="margin:0; padding:0; background-color:#f5f5f5;">

    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                
                <table width="600" border="0" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                    
                    <!-- HEADER AZUL -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #2196F3 0%, #0277BD 100%); padding: 40px 30px; text-align: center;">
                            <img src="{{ $message->embed(public_path('logo.png')) }}" alt="Dios Padre Hotel" width="120" style="max-width: 120px; height: auto; display: block; margin: 0 auto;">
                        </td>
                    </tr>

                    <!-- TÍTULO -->
                    <tr>
                        <td style="padding: 30px 30px 20px; text-align: center;">
                            <h1 style="color: #0277BD; font-size: 26px; margin: 0; font-weight: 600;">¿Olvidaste tu contraseña?</h1>
                            <p style="color: #666; font-size: 16px; margin: 10px 0 0;">No te preocupes, es fácil recuperarla</p>
                        </td>
                    </tr>

                    <!-- CONTENIDO -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <p style="color: #555; font-size: 15px; line-height: 1.8; margin: 0 0 20px;">
                                Hola, <strong>{{ $notifiable->name }}</strong>.
                            </p>
                            <p style="color: #555; font-size: 15px; line-height: 1.8; margin: 0 0 20px;">
                                Recibimos una solicitud para restablecer la contraseña de tu cuenta en <strong>Dios Padre Hotel</strong>. Si fuiste tú, simplemente haz clic en el botón de abajo para crear una nueva contraseña.
                            </p>

                            <!-- BOTÓN -->
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%); border-radius: 6px; padding: 0;">
                                                    <a href="{{ $url }}" style="display: inline-block; padding: 14px 40px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 6px;">
                                                        Restablecer Contraseña
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- AVISO DE SEGURIDAD -->
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #fff3e0; border-radius: 6px; padding: 15px; margin-bottom: 20px; border-left: 4px solid #FF9800;">
                                <tr>
                                    <td>
                                        <p style="color: #E65100; font-size: 13px; margin: 0;">
                                            <strong>⏳ Importante:</strong> Este enlace expirará en 60 minutos por seguridad.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #666; font-size: 14px; line-height: 1.6; margin: 20px 0 0;">
                                Si no solicitaste este cambio, no es necesario que hagas nada. Tu cuenta sigue segura.
                            </p>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background-color: #f5f5f5; border-top: 1px solid #eee; padding: 25px 30px; text-align: center;">
                            <p style="color: #888; font-size: 12px; margin: 0;">
                                © {{ date('Y') }} Dios Padre Hotel. Todos los derechos reservados.<br>
                                Si tienes problemas con el botón, copia y pega este enlace en tu navegador:<br>
                                <a href="{{ $url }}" style="color: #2196F3; font-size: 11px; word-break: break-all;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>