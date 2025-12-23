<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background-color: #f3f4f6; padding: 20px; }
        .container { max-w-600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #e5e7eb; padding-bottom: 20px; margin-bottom: 20px; }
        .h1 { color: #1f2937; margin: 0; }
        .details { background-color: #f9fafb; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 10px; color: #4b5563; }
        .detail-value { font-weight: bold; color: #111827; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #9ca3af; }
        .btn { display: inline-block; background-color: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Hola, {{ $appointment->patient->name }}</h1>
            <p style="color: #6b7280;">Te recordamos tu turno médico.</p>
        </div>

        <div class="details">
            <div class="detail-row">
                <span>Fecha:</span>
                <span class="detail-value">{{ $appointment->start_time->format('d/m/Y') }}</span>
            </div>
            <div class="detail-row">
                <span>Hora:</span>
                <span class="detail-value">{{ $appointment->start_time->format('H:i') }} hs</span>
            </div>
            <div class="detail-row">
                <span>Tratamiento:</span>
                <span class="detail-value">{{ $appointment->type->name }}</span>
            </div>
            <div class="detail-row">
                <span>Dirección:</span>
                <span class="detail-value">Av. Salud 123, Consultorio 4</span>
            </div>
        </div>

        <div style="text-align: center;">
            <p>Por favor, intenta llegar 10 minutos antes.</p>
            <a href="{{ url('/') }}" class="btn">Ver en mi Cuenta</a>
        </div>

        <div class="footer">
            <p>Este es un mensaje automático, no respondas a este correo.</p>
        </div>
    </div>
</body>
</html>