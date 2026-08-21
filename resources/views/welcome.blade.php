<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPTeX - Bolsa de Trabajo Inteligente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos opcionales de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/uptex-bootstrap-theme.css') }}">
    <style>
        :root {
            --uptex-verde: #006837; 
            --uptex-rojo: #C1272D;  
            --uptex-blanco: #FFFFFF;
            --texto-oscuro: #333333;
            --fondo-gris: #f8f9fa;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--fondo-gris);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* BARRA DE NAVEGACIÓN SUPERIOR */
        .navbar-top {
            background-color: var(--uptex-blanco);
            padding: 15px 50px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            color: var(--uptex-verde);
            font-weight: 800;
            font-size: 24px;
            text-decoration: none;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-botones {
            display: flex;
            gap: 15px;
        }

        .btn-nav {
            padding: 8px 20px;
            border-radius: 5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 15px;
        }

        .btn-nav-outline {
            color: var(--uptex-verde);
            border: 2px solid var(--uptex-verde);
            background: transparent;
        }

        .btn-nav-outline:hover {
            background: var(--uptex-verde);
            color: var(--uptex-blanco);
        }

        .btn-nav-solid {
            background: var(--uptex-verde);
            color: var(--uptex-blanco);
            border: 2px solid var(--uptex-verde);
        }

        .btn-nav-solid:hover {
            background: #004d28;
            border-color: #004d28;
            color: var(--uptex-blanco);
        }

        /* CONTENEDOR PRINCIPAL - DISEÑO DIVIDIDO */
        .main-container {
            display: flex;
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            align-items: center;
            gap: 60px;
        }

        /* SECCIÓN DE INFORMACIÓN (IZQUIERDA) */
        .info-section {
            flex: 1;
        }

        .info-section h1 {
            color: var(--texto-oscuro);
            font-size: 48px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .info-section h1 span {
            color: var(--uptex-verde);
        }

        .info-section p {
            color: #555;
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .caracteristicas {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .caracteristica-item {
            background: var(--uptex-blanco);
            padding: 20px;
            border-radius: 8px;
            border-left: 5px solid var(--uptex-rojo);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .caracteristica-item h3 {
            color: var(--uptex-verde);
            font-size: 18px;
            margin: 0 0 10px 0;
            font-weight: 700;
        }

        .caracteristica-item p {
            margin: 0;
            font-size: 15px;
            color: #666;
        }

        /* SECCIÓN DEL LOGIN (DERECHA) - ESTILOS ORIGINALES CONSERVADOS */
        .login-wrapper {
            flex: 0 0 420px;
        }

        .login-container {
            background-color: var(--uptex-blanco);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            width: 100%;
            border-top: 6px solid var(--uptex-rojo); 
        }

        .encabezado {
            text-align: center;
            margin-bottom: 30px;
        }

        .encabezado h1 {
            color: var(--uptex-verde);
            margin: 0;
            font-size: 42px;
            font-weight: 800;
            letter-spacing: 2px;
        }

        .encabezado h2 {
            color: var(--texto-oscuro);
            margin: 5px 0 0;
            font-size: 13px;
            font-weight: normal;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .encabezado .subtitulo {
            display: block;
            margin-top: 15px;
            color: var(--uptex-verde);
            font-weight: 600;
            font-size: 18px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 15px;
        }

        .form-group { margin-bottom: 20px; }

        label { 
            display: block; 
            margin-bottom: 8px; 
            color: var(--texto-oscuro);
            font-weight: 600;
            font-size: 14px;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 15px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="email"]:focus, input[type="password"]:focus {
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

        .btn-principal:hover { background-color: #004d28; }

        .enlace-olvido {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: var(--uptex-verde);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.3s;
        }

        .enlace-olvido:hover {
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

        .footer-principal {
            background: #222;
            color: #aaa;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            margin-top: auto;
        }

        /* RESPONSIVO */
        @media (max-width: 992px) {
            .main-container {
                flex-direction: column;
                padding: 30px 20px;
                gap: 28px;
            }
            .info-section { width: 100%; }
            .login-wrapper {
                width: 100%;
                max-width: 420px;
                flex-basis: auto;
            }
            .info-section h1 {
                font-size: 36px;
            }
        }

        @media (max-width: 576px) {
            .navbar-top {
                padding: 15px 20px;
                flex-direction: row;
                gap: 15px;
            }
            .navbar-brand { font-size: 20px; }
            .btn-nav { padding: 8px 14px; font-size: 14px; }
            .main-container { padding: 22px 14px; }
            .info-section h1 { font-size: 30px; }
            .info-section p { font-size: 15px; }
            .login-container { padding: 28px 18px; }
        }
    </style>
</head>
<body>

    <!-- BARRA SUPERIOR -->
    <nav class="navbar-top">
        <a href="/" class="navbar-brand">
            <i class="bi bi-briefcase-fill"></i> UPTeX
        </a>
        <div class="navbar-botones">
            <a href="/registro" class="btn-nav btn-nav-outline">Registrarse</a>
        </div>
    </nav>

    <!-- CONTENEDOR PRINCIPAL -->
    <main class="main-container">
        
        <!-- SECCIÓN INFORMATIVA DEL SISTEMA -->
        <section class="info-section">
            <h1>Impulsa tu futuro profesional con la <span>Bolsa de Trabajo UPTeX</span></h1>
            <p>El puente oficial entre el talento de la Universidad Politécnica de Texcoco y las empresas líderes de la región. Una plataforma diseñada exclusivamente para nuestra comunidad universitaria.</p>
            
            <div class="caracteristicas">
                <div class="caracteristica-item">
                    <h3><i class="bi bi-mortarboard"></i> Para Estudiantes y Egresados</h3>
                    <p>Encuentra vacantes acordes a tu perfil, postúlate fácilmente a prácticas profesionales o empleos formales y haz que tu currículum llegue a las empresas correctas.</p>
                </div>
                <div class="caracteristica-item">
                    <h3><i class="bi bi-buildings"></i> Para Empresas</h3>
                    <p>Publica tus ofertas laborales y recluta al mejor talento capacitado con excelencia académica y tecnológica listos para sumar valor a tu organización.</p>
                </div>
            </div>
        </section>

        <!-- SECCIÓN DEL LOGIN (ORIGINAL) -->
        <section class="login-wrapper" id="area-login">
            <div class="login-container">
                <div class="encabezado">
                    <h1>UPTeX</h1>
                    <h2>Universidad Politécnica de Texcoco</h2>
                    <span class="subtitulo">Iniciar Sesión</span>
                </div>
                
                <form id="loginForm">
                    <div class="form-group">
                        <label for="correo">Correo Electrónico Institucional</label>
                        <input type="email" id="correo" required placeholder="matricula@uptex.edu.mx">
                    </div>
                    <div class="form-group">
                        <label for="contrasena">Contraseña</label>
                        <input type="password" id="contrasena" required placeholder="Ingresa tu contraseña">
                    </div>
                    <button type="submit" class="btn-principal">Ingresar al Sistema</button>
                </form>

                <a href="/recuperar" class="enlace-olvido">¿Olvidaste tu contraseña?</a>
                <div id="mensaje"></div>
            </div>
        </section>

    </main>

    <!-- FOOTER -->
    <footer class="footer-principal">
        &copy; 2026 Universidad Politécnica de Texcoco. Todos los derechos reservados.<br>
        Bolsa de Trabajo Inteligente.
    </footer>

    <!-- SCRIPT ORIGINAL (Funcionalidad intacta) -->
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const correo = document.getElementById('correo').value.trim();
            const contrasena = document.getElementById('contrasena').value;

            const boton = document.querySelector('.btn-principal');
            const divMensaje = document.getElementById('mensaje');

            const textoOriginal = boton.innerHTML;

            boton.disabled = true;
            boton.innerHTML = 'Verificando...';
            divMensaje.style.display = 'none';

            try {
                const respuesta = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        correo: correo,
                        contrasena: contrasena
                    })
                });

                const data = await respuesta.json();

                if (!respuesta.ok) {
                    throw new Error(data.mensaje || 'Correo o contraseña incorrectos.');
                }

                sessionStorage.clear();
                localStorage.clear();

                // Guardar información del usuario
                sessionStorage.setItem('usuario_nombre', data.usuario.nombre);
                sessionStorage.setItem('usuario_correo', data.usuario.correo);
                const rol = normalizarRol(data.usuario.rol);
                sessionStorage.setItem('usuario_rol', rol);

                // Guardar token (para futuras APIs)
                if (data.token) {
                    sessionStorage.setItem('token_acceso', data.token);
                }

                switch (rol) {
                    case 'admin':
                        window.location.href = '/dashboard/administrador';
                        break;
                    case 'empresa':
                        window.location.href = '/dashboard/empresa';
                        break;
                    case 'egresado':
                        window.location.href = '/dashboard/egresado';
                        break;
                    default:
                        window.location.href = '/dashboard';
                        break;
                }

            } catch (error) {
                boton.disabled = false;
                boton.innerHTML = textoOriginal;

                divMensaje.style.display = 'block';
                divMensaje.className = 'error';
                divMensaje.innerHTML = error.message;
            }
        });

        function normalizarRol(rol) {
            const valor = String(rol || '').toLowerCase().trim();
            const alias = {
                administrador: 'admin',
                administrator: 'admin',
                compania: 'empresa',
                compañia: 'empresa',
                company: 'empresa',
                alumno: 'egresado',
                estudiante: 'egresado',
                usuario: 'egresado'
            };

            return alias[valor] || valor || 'egresado';
        }
    </script>
</body>
</html>
