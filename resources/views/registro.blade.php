<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPTeX - Registro de Egresados</title>
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

        .registro-container {
            background-color: var(--uptex-blanco);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            border-top: 6px solid var(--uptex-rojo);
        }

        .encabezado {
            text-align: center;
            margin-bottom: 25px;
        }

        .encabezado h1 {
            color: var(--uptex-verde);
            margin: 0;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .encabezado .subtitulo {
            display: block;
            margin-top: 10px;
            color: var(--texto-oscuro);
            font-weight: 600;
            font-size: 16px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 15px;
        }

        .form-group {
            margin-bottom: 20px;
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--texto-oscuro);
            font-weight: 600;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 15px;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        input:focus {
            border-color: var(--uptex-verde);
            outline: none;
            box-shadow: 0 0 5px rgba(0, 104, 55, 0.3);
        }

        .btn-principal {
            width: 100%;
            padding: 14px;
            background-color: var(--uptex-verde);
            color: var(--uptex-blanco);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .btn-principal:hover {
            background-color: #004d28;
        }

        .btn-regresar {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--texto-oscuro);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.3s;
        }

        .btn-regresar:hover {
            color: var(--uptex-rojo);
            text-decoration: underline;
        }

        #mensaje {
            margin-top: 20px;
            padding: 12px;
            border-radius: 5px;
            text-align: center;
            display: none;
            font-size: 14px;
            font-weight: 600;
        }

        .exito {
            background-color: #e8f5e9;
            color: var(--uptex-verde);
            border: 1px solid var(--uptex-verde);
        }

        .error {
            background-color: #ffebee;
            color: var(--uptex-rojo);
            border: 1px solid var(--uptex-rojo);
        }

        .requisitos-contrasena {
            margin-top: 8px;
            padding: 10px 12px;
            background: #f8faf9;
            border: 1px solid #dde8e1;
            border-radius: 6px;
            font-size: 13px;
            color: #5f6b66;
        }

        .requisitos-contrasena ul {
            margin: 6px 0 0;
            padding-left: 18px;
        }

        .requisitos-contrasena li {
            margin: 3px 0;
        }

        .requisitos-contrasena li.valido {
            color: var(--uptex-verde);
            font-weight: 600;
        }

        .requisitos-contrasena li.invalido {
            color: var(--uptex-rojo);
        }

        @media (max-width: 576px) {
            body { align-items: flex-start; padding: 14px; }
            .registro-container { padding: 26px 18px; }
            .encabezado h1 { font-size: 28px; }
            .btn-principal { font-size: 14px; white-space: normal; }
        }
    </style>
</head>

<body>

<div class="registro-container">

    <div class="encabezado">
        <h1>UPTeX</h1>
        <span class="subtitulo">Registro de Egresados</span>
    </div>

    <form id="registroForm">

        <div class="form-group">
            <label for="nombre">Nombre Completo</label>
            <input
                type="text"
                id="nombre"
                required
                placeholder="Ej. Juan Perez Garcia"
            >
        </div>

        <div class="form-group">
            <label for="correo">Correo Electronico Institucional</label>
            <input
                type="email"
                id="correo"
                required
                placeholder="matricula@uptex.edu.mx"
            >
        </div>

        <div class="form-group">
            <label for="contrasena">Contrasena</label>
            <input
                type="password"
                id="contrasena"
                required
                minlength="8"
                pattern="(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}"
                placeholder="Minimo 8 caracteres"
            >
            <div class="requisitos-contrasena" aria-live="polite">
                <strong>La contrasena debe incluir:</strong>
                <ul>
                    <li id="req-longitud" class="invalido">Al menos 8 caracteres</li>
                    <li id="req-mayuscula" class="invalido">Una letra mayuscula</li>
                    <li id="req-numero" class="invalido">Un numero</li>
                    <li id="req-especial" class="invalido">Un caracter especial</li>
                </ul>
            </div>
        </div>

        <button type="submit" class="btn-principal">
            Registrarse
        </button>

    </form>

    <div id="mensaje"></div>

    <a href="/" class="btn-regresar">
        <- Volver al inicio de sesion
    </a>

</div>

<script>

function validarContrasena(contrasena) {
    return {
        longitud: contrasena.length >= 8,
        mayuscula: /[A-Z]/.test(contrasena),
        numero: /\d/.test(contrasena),
        especial: /[^A-Za-z0-9]/.test(contrasena)
    };
}

function actualizarRequisitosContrasena() {
    const contrasena = document.getElementById('contrasena').value;
    const reglas = validarContrasena(contrasena);

    Object.entries({
        'req-longitud': reglas.longitud,
        'req-mayuscula': reglas.mayuscula,
        'req-numero': reglas.numero,
        'req-especial': reglas.especial
    }).forEach(([id, valido]) => {
        const item = document.getElementById(id);
        item.classList.toggle('valido', valido);
        item.classList.toggle('invalido', !valido);
    });

    return Object.values(reglas).every(Boolean);
}

document.getElementById('contrasena').addEventListener('input', actualizarRequisitosContrasena);

document.getElementById('registroForm').addEventListener('submit', async function(e) {

    e.preventDefault();

    const nombre = document.getElementById('nombre').value;
    const rol = 'egresado'; // Asignado automaticamente sin preguntarle al usuario
    const correo = document.getElementById('correo').value;
    const contrasena = document.getElementById('contrasena').value;

    const divMensaje = document.getElementById('mensaje');
    const boton = document.querySelector('.btn-principal');

    if (!actualizarRequisitosContrasena()) {
        divMensaje.style.display = 'block';
        divMensaje.className = 'error';
        divMensaje.innerHTML = 'La contrasena debe tener al menos 8 caracteres, una mayuscula, un numero y un caracter especial.';
        return;
    }

    const textoOriginal = boton.innerHTML;

    boton.innerHTML = 'Registrando...';
    boton.disabled = true;

    try {

        const response = await fetch('/api/registro', {

            method: 'POST',

            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },

            body: JSON.stringify({
                nombre: nombre,
                rol: rol,
                correo: correo,
                contrasena: contrasena
            })

        });

        const data = await response.json();

        boton.innerHTML = textoOriginal;
        boton.disabled = false;

        divMensaje.style.display = 'block';

        // Registro exitoso
        if (response.ok) {

            divMensaje.className = 'exito';

            divMensaje.innerHTML = `
                <strong>¡Usuario registrado correctamente!</strong>
                <br>
                Redirigiendo al inicio de sesion...
            `;

            // Limpiar formulario
            document.getElementById('registroForm').reset();

            // Redireccionar al login
            setTimeout(() => {
                window.location.href = '/';
            }, 1500);

        } else {
            // Error de validacion (por ejemplo, el correo ya existe)
            divMensaje.className = 'error';

            divMensaje.innerHTML =
                data.mensaje ||
                data.errors?.contrasena?.[0] ||
                data.errors?.correo?.[0] || // Extrae el error especifico de Laravel si el correo se repite
                'No se pudo registrar el usuario';
        }

    } catch (error) {

        console.error('Error:', error);

        boton.innerHTML = textoOriginal;
        boton.disabled = false;

        divMensaje.style.display = 'block';
        divMensaje.className = 'error';

        divMensaje.innerHTML =
            'Error de conexion con el servidor';

    }

});

</script>

</body>
</html>


