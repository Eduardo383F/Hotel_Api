<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reserva Confirmada</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; }
        .detail-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .detail-table tr:last-child td { border-bottom: none; }
    </style>
</head>
<body style="margin:0; padding:0; background-color:#f5f5f5;">

    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table width="600" border="0" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                    
                    <!-- HEADER -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #2196F3 0%, #0277BD 100%); padding: 30px; text-align: center;">
                            <!-- Logo incrustado -->
                            <img src="{{ $message->embed(public_path('logo.png')) }}" alt="Logo" width="100" style="display:block; margin:0 auto;">
                            <h2 style="color: white; margin: 15px 0 0;">¡Tu Reserva está Confirmada!</h2>
                        </td>
                    </tr>

                    <!-- INFO PRINCIPAL -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="color: #555; font-size: 16px;">Hola <strong>{{ $reservation->user->name }}</strong>,</p>
                            <p style="color: #666; line-height: 1.6;">Gracias por elegir <strong>Hotel Dios Padre</strong>. Tu pago ha sido procesado exitosamente y tu habitación te espera.</p>

                            <!-- DETALLES DE RESERVA -->
                            <table class="detail-table" width="100%" style="background-color: #f9f9f9; border-radius: 6px; margin: 20px 0; border: 1px solid #eee;">
                                <tr>
                                    <td width="40%" style="color:#888;">Folio:</td>
                                    <td style="font-weight:bold; color:#333;">#{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#888;">Habitación:</td>
                                    <td style="font-weight:bold; color:#333;">
                                        {{ $reservation->room->number }} - {{ $reservation->room->type->name ?? 'Estándar' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color:#888;">Check-in:</td>
                                    <td style="color:#333;">{{ \Carbon\Carbon::parse($reservation->check_in)->format('d/m/Y') }} (14:00 PM)</td>
                                </tr>
                                <tr>
                                    <td style="color:#888;">Check-out:</td>
                                    <td style="color:#333;">{{ \Carbon\Carbon::parse($reservation->check_out)->format('d/m/Y') }} (12:00 PM)</td>
                                </tr>
                                <tr>
                                    <td style="color:#888;">Huéspedes:</td>
                                    <td style="color:#333;">{{ $reservation->adults }} Adultos, {{ $reservation->children }} Niños</td>
                                </tr>
                                <tr>
                                    <td style="color:#888;">Total Pagado:</td>
                                    <td style="font-weight:bold; color:#2e7d32;">${{ number_format($reservation->total_price, 2) }} MXN</td>
                                </tr>
                            </table>

                            <!-- BOTONES DE ACCIÓN -->
                            <div style="text-align: center; margin: 30px 0;">
                                
                                <!-- 1. BOTÓN VER EN WEB (React) -->
                                <a href="{{ config('app.frontend_url', 'http://localhost:3000') }}/voucher/{{ $reservation->id }}" 
                                   style="background-color: #0277BD; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 14px; margin: 5px; display: inline-block;">
                                    Ver Código QR
                                </a>

                                <!-- 2. BOTÓN DESCARGAR PDF (Laravel Signed Route) -->
                                <!-- Usamos la ruta firmada para permitir descarga segura desde el correo -->
                                <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('reservations.pdf', ['id' => $reservation->id]) }}" 
                                   style="background-color: #f57c00; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 14px; margin: 5px; display: inline-block;">
                                    Descargar Comprobante PDF
                                </a>

                                <p style="font-size: 12px; color: #999; margin-top: 15px;">
                                    Necesitarás uno de estos comprobantes para ingresar a tu habitación.
                                </p>
                            </div>

                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background-color: #f5f5f5; padding: 20px; text-align: center; color: #999; font-size: 12px;">
                            © {{ date('Y') }} Hotel Dios Padre. ¿Dudas? Contáctanos al (771) 410 6182.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>