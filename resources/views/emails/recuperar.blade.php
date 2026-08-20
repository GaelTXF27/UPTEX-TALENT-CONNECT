<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; background-color: #f4f7f6; color: #333; padding: 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; max-width: 500px; margin: 0 auto; border-top: 5px solid #006837; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        h2 { color: #006837; margin-top: 0; }
        .codigo { font-size: 24px; font-weight: bold; background: #f0f0f0; padding: 10px; text-align: center; border-radius: 4px; letter-spacing: 4px; color: #c1272d; margin: 20px 0; }
        .nota { font-size: 12px; color: #777; margin-top: 25px; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Restablecer Contrasena</h2>
        <p>Has solicitado restablecer tu contrasena en la plataforma de la Bolsa de Trabajo UPTeX.</p>
        <p>Utiliza el siguiente codigo para validar tu identidad en la pantalla de recuperacion:</p>
        
        <div class="codigo">{{ $token }}</div>
        
        <p>Este codigo es de un solo uso y expirara pronto.</p>
        <p class="nota">Si no solicitaste este cambio, puedes ignorar este correo de forma segura.</p>
    </div>
</body>
</html>

