<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Bienvenido a Dios Padre Hotel!</title>
    <style type="text/css">
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        table {
            border-collapse: collapse;
        }
        img {
            border: 0;
            outline: 0;
        }
    </style>
</head>
<body style="margin:0; padding:0; background-color:#f5f5f5;">

    <!-- CONTENEDOR PRINCIPAL -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                
                <!-- EMAIL WRAPPER -->
                <table width="600" border="0" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                    
                    <!-- HEADER CON LOGO Y GRADIENTE -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #2196F3 0%, #0277BD 100%); padding: 40px 30px; text-align: center;">
                            <!-- AQUÍ HACEMOS LA MAGIA PARA QUE EL LOGO SE VEA SIEMPRE -->
                            <img src="{{ $message->embed(public_path('logo.png')) }}" alt="Dios Padre Hotel" width="120" style="max-width: 120px; height: auto; display: block; margin: 0 auto;">
                        </td>
                    </tr>

                    <!-- SALUDO -->
                    <tr>
                        <td style="padding: 30px 30px 20px; text-align: center;">
                            <h1 style="color: #0277BD; font-size: 28px; margin: 0; font-weight: 600;">¡Bienvenido, {{ $user->name }}!</h1>
                            <p style="color: #666; font-size: 16px; margin: 10px 0 0; line-height: 1.4;">Tu cuenta ha sido creada exitosamente</p>
                        </td>
                    </tr>

                    <!-- CONTENIDO PRINCIPAL -->
                    <tr>
                        <td style="padding: 0 30px 30px;">
                            <p style="color: #555; font-size: 15px; line-height: 1.8; margin: 0 0 20px;">
                                Gracias por registrarte en <strong>Dios Padre Hotel</strong>. Nos complace mucho que hayas elegido nuestra plataforma para planificar tu próxima estancia. Nuestro equipo se dedica a ofrecerte la mejor experiencia en hospedaje.
                            </p>

                            <p style="color: #555; font-size: 15px; line-height: 1.8; margin: 0 0 25px;">
                                Con tu nueva cuenta, ahora puedes:
                            </p>

                            <!-- LISTA DE BENEFICIOS -->
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin: 0 0 30px;">
                                <tr>
                                    <td style="padding: 12px 0; border-left: 4px solid #2196F3; padding-left: 15px;">
                                        <p style="color: #333; font-size: 15px; font-weight: 600; margin: 0;">✓ Explorar Habitaciones</p>
                                        <p style="color: #888; font-size: 13px; margin: 3px 0 0;">Descubre nuestras suites y habitaciones exclusivas con vistas panorámicas.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-left: 4px solid #2196F3; padding-left: 15px;">
                                        <p style="color: #333; font-size: 15px; font-weight: 600; margin: 0;">✓ Reservar en Segundos</p>
                                        <p style="color: #888; font-size: 13px; margin: 3px 0 0;">Proceso seguro y rápido de reservación con confirmación inmediata.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-left: 4px solid #2196F3; padding-left: 15px;">
                                        <p style="color: #333; font-size: 15px; font-weight: 600; margin: 0;">✓ Ofertas Especiales</p>
                                        <p style="color: #888; font-size: 13px; margin: 3px 0 0;">Acceso exclusivo a promociones y descuentos para miembros registrados.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-left: 4px solid #2196F3; padding-left: 15px;">
                                        <p style="color: #333; font-size: 15px; font-weight: 600; margin: 0;">✓ Soporte Prioritario</p>
                                        <p style="color: #888; font-size: 13px; margin: 3px 0 0;">Equipo de atención disponible 24/7 para resolver tus dudas.</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- BOTÓN CTA -->
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin: 35px 0;">
                                <tr>
                                    <td align="center">
                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%); border-radius: 6px; padding: 0;">
                                                    <!-- Usamos url('/') como fallback si no hay config de frontend -->
                                                    <a href="{{ config('app.frontend_url', url('/')) }}/dashboard" style="display: inline-block; padding: 14px 40px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 6px;">
                                                        Ir a Mi Cuenta
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CIERRE -->
                            <p style="color: #666; font-size: 15px; line-height: 1.8; margin: 20px 0;">
                                Si tienes preguntas o necesitas asistencia, nuestro equipo de soporte está disponible 24/7. No dudes en contactarnos.
                            </p>

                            <p style="color: #666; font-size: 15px; line-height: 1.8; margin: 0;">
                                <strong>¡Que disfrutes tu próxima estadía con nosotros!</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background-color: #f5f5f5; border-top: 1px solid #eee; padding: 25px 30px; text-align: center;">
                            
                            <!-- DATOS DE CONTACTO -->
                            <p style="color: #888; font-size: 12px; margin: 0 0 8px;">
                                <strong>Dios Padre Hotel</strong> | Ixmiquilpan, Hidalgo<br>
                                📞 (771) 410 6182 | 📧 contacto@diospadrehotel.com
                            </p>

                            <!-- COPYRIGHT -->
                            <p style="color: #aaa; font-size: 11px; margin: 10px 0 0;">
                                © {{ date('Y') }} Dios Padre Hotel. Todos los derechos reservados.<br>
                                <a href="#" style="color: #2196F3; text-decoration: none; font-size: 11px;">Política de Privacidad</a> | 
                                <a href="#" style="color: #2196F3; text-decoration: none; font-size: 11px;">Términos de Servicio</a>
                            </p>

                            <!-- NOTA IMPORTANTE -->
                            <p style="color: #999; font-size: 11px; margin: 15px 0 0; font-style: italic;">
                                Si no creaste esta cuenta, puedes ignorar este mensaje de forma segura.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- FIN EMAIL WRAPPER -->

            </td>
        </tr>
    </table>

</body>
</html>