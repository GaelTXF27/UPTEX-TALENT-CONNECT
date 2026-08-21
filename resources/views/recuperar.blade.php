<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPTeX - Recuperar Contrasena</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/uptex-bootstrap-theme.css') }}">
    <style>
        :root {
            --uptex-verde: #006837; 
            --uptex-rojo: #C1272D;  
            --uptex-blanco: #FFFFFF;
            --texto-oscuro: #333333;
            --fondo-gris: #f0f4f1;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--fondo-gris);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            overflow-y: auto;
        }
        .recuperar-container {
            background-color: var(--uptex-blanco);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 420px;
            border-top: 6px solid var(--uptex-rojo); 
        }

        @media (max-width: 576px) {
            body { align-items: flex-start; padding: 14px; }
            .recuperar-container { padding: 26px 18px; }
            .encabezado h1 { font-size: 28px; }
            .btn-principal { font-size: 14px; white-space: normal; }
        }
        .encabezado { text-align: center; margin-bottom: 25px; }
        .encabezado h1 { color: var(--uptex-verde); margin: 0; font-size: 32px; font-weight: 800; }
        .encabezado .subtitulo { display: block; margin-top: 10px; color: var(--texto-oscuro); font-weight: 600; font-size: 16px; border-bottom: 2px solid #e0e0e0; padding-bottom: 15px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: var(--texto-oscuro); font-weight: 600; font-size: 14px; }
        input[type="email"], input[type="text"], input[type="password"] {
            width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 15px;
        }
        input:focus { border-color: var(--uptex-verde); outline: none; box-shadow: 0 0 5px rgba(0, 104, 55, 0.3); }
        .btn-principal {
            width: 100%; padding: 14px; background-color: var(--uptex-verde); color: var(--uptex-blanco); border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; text-transform: uppercase; margin-top: 10px; transition: 0.3s;
        }
        .btn-principal:hover { background-color: #004d28; }
        .btn-regresar { display: block; text-align: center; margin-top: 20px; color: var(--texto-oscuro); text-decoration: none; font-size: 14px; font-weight: 600; }
        .btn-regresar:hover { color: var(--uptex-rojo); text-decoration: underline; }
        #mensaje { margin-top: 20px; padding: 12px; border-radius: 5px; text-align: center; display: none; font-size: 14px; font-weight: 600; }
        .exito { background-color: #e8f5e9; color: var(--uptex-verde); border: 1px solid var(--uptex-verde); }
        .error { background-color: #ffebee; color: var(--uptex-rojo); border: 1px solid var(--uptex-rojo); }
        .alerta-info { background-color: #e3f2fd; color: #0d47a1; padding: 10px; border-radius: 5px; font-size: 13px; margin-bottom: 15px; border: 1px solid #b3e5fc; text-align: center; }
    </style>
</head>
<body>

<div class="recuperar-container">
    <div class="encabezado">
        <h1>UPTeX</h1>
        <span class="subtitulo">Recuperar Contrasena</span>
    </div>

    <form id="solicitarForm">
        <p style="font-size: 14px; color: #666; text-align: center;">Ingresa tu correo y te enviaremos un codigo de validacion.</p>
        <div class="form-group">
            <label for="correo_solicitud">Correo Electronico Registrado</label>
            <input type="email" id="correo_solicitud" required placeholder="correo@ejemplo.com">
        </div>
        <button type="submit" class="btn-principal" id="btn-solicitar">Enviar Codigo</button>
    </form>

    <form id="restablecerForm" style="display: none;">
        <div class="alerta-info">
            <strong>¡Revisa tu bandeja!</strong> Hemos enviado un codigo de 6 caracteres a tu correo.
        </div>
        <input type="hidden" id="correo_confirmado">
        <div class="form-group">
            <label for="codigo_token">Codigo de Seguridad</label>
            <input type="text" id="codigo_token" required placeholder="Ej: aB3x9Q" style="font-weight: bold; text-align: center; letter-spacing: 2px;">
        </div>
        <div class="form-group">
            <label for="nueva_contrasena">Nueva Contrasena</label>
            <input type="password" id="nueva_contrasena" required minlength="6" placeholder="Minimo 6 caracteres">
        </div>
        <button type="submit" class="btn-principal" id="btn-restablecer">Guardar Contrasena</button>
    </form>

    <div id="mensaje"></div>

    <a href="/" class="btn-regresar"><- Volver al inicio de sesion</a>
</div>

<script>
    const solicitarForm = document.getElementById('solicitarForm');
    const restablecerForm = document.getElementById('restablecerForm');
    const divMensaje = document.getElementById('mensaje');

    // Manejar Fase 1: Pedir el codigo
    solicitarForm.addEventListener('submit', function(e) {
        e.preventDefault(); 
        const correo = document.getElementById('correo_solicitud').value;
        const boton = document.getElementById('btn-solicitar');
        
        boton.innerHTML = 'Buscando...'; boton.disabled = true;

        fetch('/api/solicitar-recuperacion', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ correo: correo })
        })
        .then(response => response.json())
        .then(data => {
            // CORREGIDO: Ahora validamos si el backend dice "exito: true"
            if(data.exito) { 
                // Exito: ocultamos form 1 y mostramos form 2
                solicitarForm.style.display = 'none';
                restablecerForm.style.display = 'block';
                document.getElementById('correo_confirmado').value = correo;
                
                divMensaje.style.display = 'none'; // Ocultar mensajes previos
            } else {
                boton.innerHTML = 'Enviar Codigo'; boton.disabled = false;
                divMensaje.style.display = 'block'; divMensaje.className = 'error';
                // CORREGIDO: Muestra el mensaje exacto del backend (ej: "El correo no existe")
                divMensaje.innerHTML = data.mensaje || 'Error al procesar la solicitud.'; 
            }
        })
        .catch(error => {
            boton.innerHTML = 'Enviar Codigo'; boton.disabled = false;
            divMensaje.style.display = 'block'; divMensaje.className = 'error'; divMensaje.innerHTML = 'Error de conexion con el servidor.';
        });
    });

    // Manejar Fase 2: Cambiar la contrasena
    restablecerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const correo = document.getElementById('correo_confirmado').value;
        const token = document.getElementById('codigo_token').value;
        const nuevaContrasena = document.getElementById('nueva_contrasena').value;
        const boton = document.getElementById('btn-restablecer');

        boton.innerHTML = 'Guardando...'; boton.disabled = true;

        fetch('/api/restablecer-contrasena', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ correo: correo, token: token, nueva_contrasena: nuevaContrasena })
        })
        .then(response => response.json())
        .then(data => {
            divMensaje.style.display = 'block';
            if(data.mensaje === 'Contrasena actualizada correctamente') {
                divMensaje.className = 'exito';
                divMensaje.innerHTML = '<strong>¡Exito!</strong><br>Contrasena actualizada. Redirigiendo...';
                setTimeout(() => { window.location.href = '/'; }, 2500); // Ajusta la ruta de tu login si es diferente
            } else {
                boton.innerHTML = 'Guardar Contrasena'; boton.disabled = false;
                divMensaje.className = 'error'; divMensaje.innerHTML = data.mensaje;
            }
        });
    });
</script>

</body>
</html>



