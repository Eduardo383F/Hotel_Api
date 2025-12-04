<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Reserva</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        .voucher {
            background-color: white;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #2180a1;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2180a1;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
            font-size: 14px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            background-color: #2180a1;
            color: white;
            padding: 12px 15px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: bold;
            color: #2180a1;
            width: 40%;
        }
        
        .info-value {
            text-align: right;
            width: 60%;
            color: #333;
        }
        
        .guest-section {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #2180a1;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .dates-container {
            display: flex;
            justify-content: space-around;
            gap: 20px;
            margin: 20px 0;
            padding: 20px;
            background-color: #f0f8ff;
            border-radius: 4px;
        }
        
        .date-box {
            text-align: center;
            flex: 1;
        }
        
        .date-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        
        .date-value {
            font-size: 20px;
            font-weight: bold;
            color: #2180a1;
        }
        
        .nights {
            text-align: center;
            font-size: 14px;
            color: #666;
            padding: 10px;
            background-color: #e8f4f8;
            border-radius: 4px;
            margin: 10px 0;
        }
        
        .price-summary {
            background-color: #f0f8ff;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }
        
        .price-row.total {
            border-top: 2px solid #2180a1;
            padding-top: 12px;
            margin-top: 10px;
            font-weight: bold;
            font-size: 18px;
            color: #2180a1;
        }
        
        .qr-section {
            text-align: center;
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 4px;
            margin: 30px 0;
            border: 2px dashed #2180a1;
        }
        
        .qr-section h3 {
            color: #2180a1;
            font-size: 14px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        .qr-code {
            display: inline-block;
            padding: 10px;
            background-color: white;
            border-radius: 4px;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            font-size: 12px;
            color: #999;
        }
        
        .status-badge {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0;
        }
        
        .room-type-badge {
            display: inline-block;
            background-color: #2180a1;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="voucher">
        <!-- HEADER -->
        <div class="header">
            <h1>🏨 COMPROBANTE DE RESERVA</h1>
            <p>Reserva Confirmada y Pagada</p>
            <div class="status-badge">✓ CONFIRMADO</div>
        </div>

        <!-- INFORMACIÓN DEL HUÉSPED -->
        <div class="section">
            <div class="section-title">INFORMACIÓN DEL HUÉSPED</div>
            <div class="guest-section">
                <div class="info-row">
                    <span class="info-label">Nombre:</span>
                    <span class="info-value">{{ $res->guest_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $res->email }}</span>
                </div>
                @if($res->phone)
                <div class="info-row">
                    <span class="info-label">Teléfono:</span>
                    <span class="info-value">{{ $res->phone }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Número de Reserva:</span>
                    <span class="info-value">#{{ $res->id }}</span>
                </div>
            </div>
        </div>

        <!-- INFORMACIÓN DE LA HABITACIÓN -->
        <div class="section">
            <div class="section-title">INFORMACIÓN DE LA HABITACIÓN</div>
            <div class="info-row">
                <span class="info-label">Habitación:</span>
                <span class="info-value">
                    {{ $res->room_number }}
                    <span class="room-type-badge">{{ $res->room_type }}</span>
                </span>
            </div>
        </div>

        <!-- FECHAS Y DURACIÓN -->
        <div class="section">
            <div class="section-title">FECHAS DE ESTANCIA</div>
            <div class="dates-container">
                <div class="date-box">
                    <div class="date-label">CHECK-IN</div>
                    <div class="date-value">{{ \Carbon\Carbon::parse($res->check_in)->format('d/m/Y') }}</div>
                </div>
                <div class="date-box">
                    <div class="date-label">CHECK-OUT</div>
                    <div class="date-value">{{ \Carbon\Carbon::parse($res->check_out)->format('d/m/Y') }}</div>
                </div>
            </div>
            @php
                $noches = \Carbon\Carbon::parse($res->check_in)->diffInDays(\Carbon\Carbon::parse($res->check_out));
            @endphp
            <div class="nights">
                <strong>{{ $noches }} Noche(s)</strong>
            </div>
        </div>

        <!-- RESUMEN DE PRECIOS -->
        <div class="section">
            <div class="section-title">RESUMEN DE COSTOS</div>
            <div class="price-summary">
                <div class="price-row">
                    <span>Precio Total:</span>
                    <span>${{ number_format($res->total_price, 2) }}</span>
                </div>
                @if($res->special_requests)
                <div class="price-row">
                    <span>Estado del Pago:</span>
                    <span>PAGADO</span>
                </div>
                @endif
                <div class="price-row total">
                    <span>TOTAL A PAGAR:</span>
                    <span>${{ number_format($res->total_price, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- OBSERVACIONES ESPECIALES -->
        @if($res->special_requests)
            <div class="section">
                <div class="section-title">SOLICITUDES ESPECIALES</div>
                <div style="padding: 15px; background-color: #f9f9f9; border-radius: 4px; border-left: 4px solid #2180a1;">
                    <p style="margin: 0; line-height: 1.6;">{{ $res->special_requests }}</p>
                </div>
            </div>
        @endif

        <!-- QR CODE PARA CHECK-IN -->
        @if(isset($res->qr_token) && $res->qr_token)
        <div class="qr-section">
            <h3>Código QR - Presenta en Recepción</h3>
            <div class="qr-code">
                <img src="data:image/svg+xml;base64,{{ \DNS2D::getBarcodeSVG($res->qr_token, 'QRCODE', 4, 4, 'rgb(0, 0, 0)', true) }}" 
                     alt="QR Code" width="200" height="200">
            </div>
        </div>
        @else
        <div class="qr-section">
            <h3>Código de Reserva</h3>
            <p style="font-size: 18px; font-weight: bold; color: #2180a1;">
                Reserva #{{ $res->id }}
            </p>
            <p style="font-size: 12px; color: #666;">
                Presenta este número en recepción para tu check-in
            </p>
        </div>
        @endif


        <!-- POLÍTICAS Y TÉRMINOS -->
        <div class="section">
            <div class="section-title">IMPORTANTE</div>
            <ul style="padding-left: 20px; font-size: 12px; line-height: 1.8; color: #666;">
                <li>Presenta este comprobante junto con tu documento de identidad en recepción</li>
                <li>El check-in es a partir de las 15:00 horas</li>
                <li>El check-out debe realizarse antes de las 11:00 horas</li>
                <li>Reserva no reembolsable (según términos y condiciones)</li>
                <li>Cualquier pregunta, contacta a: info@hotel.com</li>
            </ul>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p><strong>Hotel Resort & Spa</strong></p>
            <p>Dirección: Calle Principal 123, Ciudad, País</p>
            <p>Teléfono: +1 (555) 123-4567 | Email: info@hotel.com</p>
            <p style="margin-top: 15px; border-top: 1px solid #ddd; padding-top: 15px;">
                Comprobante generado automáticamente el {{ now()->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>
</body>
</html>
