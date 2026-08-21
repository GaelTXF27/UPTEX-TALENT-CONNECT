<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPTeX - Dashboard Analitico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/uptex-bootstrap-theme.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --uptex-verde: #006837;
            --uptex-rojo: #C1272D;
            --uptex-blanco: #FFFFFF;
            --texto-oscuro: #333333;
            --texto-mutado: #666666;
            --fondo-gris: #f4f7f6;
            --borde-color: #e0e0e0;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--fondo-gris);
            margin: 0;
            overflow-x: hidden;
            display: block;
            min-height: 100vh;
            color: var(--texto-oscuro);
        }

        .sidebar {
            width: var(--sidebar-width);
            background-color: white;
            color: var(--texto-oscuro);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            z-index: 1200;
            flex-shrink: 0;
            border-right: 1px solid var(--borde-color);
            box-shadow: 0 10px 35px rgba(0,0,0,0.08);
            position: fixed;
            inset: 0 auto 0 0;
            height: 100vh;
        }

        .sidebar.oculto { transform: translateX(-100%); }
        .sidebar-header { background-color: var(--uptex-verde); padding: 20px; text-align: center; font-size: 22px; font-weight: 800; color: var(--uptex-blanco); letter-spacing: 1px; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; flex-grow: 1; }
        .sidebar-menu li { border-bottom: 1px solid #f0f3f1; }
        .sidebar-menu a,
        .sidebar-menu button { width: 100%; border: 0; background: transparent; color: #5f6b66; text-decoration: none; padding: 15px 20px; display: flex; align-items: center; gap: 15px; transition: 0.2s; font-weight: 600; text-align: left; }
        .sidebar-menu a:hover,
        .sidebar-menu button:hover,
        .sidebar-menu a.activo,
        .sidebar-menu button.activo { background-color: #e8f0eb; color: var(--uptex-verde); border-right: 4px solid var(--uptex-verde); }

        .wrapper {
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            min-width: 0;
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            overflow-x: hidden;
        }

        .navbar {
            background-color: var(--uptex-blanco);
            color: var(--texto-oscuro);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 3000;
        }
        .navbar > * { min-width: 0; }

        .toggle-btn { background: none; border: none; color: var(--uptex-verde); font-size: 20px; cursor: pointer; }
        .usuario-info { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
        .etiqueta-rol { background-color: var(--uptex-blanco); color: var(--uptex-verde); padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .btn-logout { background-color: var(--uptex-rojo); color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .notificaciones { position: relative; }
        .btn-notificaciones {
            background: transparent;
            color: var(--uptex-blanco);
            border: none;
            cursor: pointer;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: background-color 0.2s;
        }
        .btn-notificaciones:hover { background-color: #e8f0eb; }
        .btn-notificaciones { color: var(--uptex-verde); }
        .badge-notificaciones {
            position: absolute;
            top: 1px;
            right: 1px;
            background-color: var(--uptex-rojo);
            color: white;
            min-width: 16px;
            height: 16px;
            padding: 0 4px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: bold;
            display: none;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--uptex-verde);
        }
        .panel-notificaciones {
            display: none;
            position: fixed;
            left: 12px;
            top: 64px;
            width: min(340px, calc(100vw - 28px));
            background: white;
            color: var(--texto-oscuro);
            border: 1px solid var(--borde-color);
            border-radius: 8px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.18);
            overflow: auto;
            resize: both;
            min-width: 260px;
            min-height: 170px;
            max-width: calc(100vw - 24px);
            max-height: calc(100vh - 24px);
            z-index: 5000;
        }
        .panel-notificaciones.activo {
            display: flex;
            flex-direction: column;
        }
        .panel-notificaciones::after {
            content: "";
            position: absolute;
            right: 6px;
            bottom: 6px;
            width: 14px;
            height: 14px;
            border-right: 2px solid #b8c2bd;
            border-bottom: 2px solid #b8c2bd;
            pointer-events: none;
        }
        .panel-notificaciones header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            background: #f8faf9;
            border-bottom: 1px solid var(--borde-color);
            font-weight: 700;
            cursor: default;
            user-select: none;
        }
        .panel-notificaciones header button {
            background: none;
            border: none;
            color: var(--uptex-verde);
            cursor: pointer;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }
        .lista-notificaciones {
            flex: 1 1 auto;
            min-height: 100px;
            overflow-y: auto;
        }
        .notificacion-item {
            padding: 12px 14px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .notificacion-item:last-child { border-bottom: none; }
        .notificacion-icono {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e8f0eb;
            color: var(--uptex-verde);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .notificacion-texto strong { display: block; font-size: 13px; margin-bottom: 3px; }
        .notificacion-texto span { display: block; font-size: 12px; color: var(--texto-mutado); line-height: 1.35; }
        .notificacion-texto { min-width: 0; overflow-wrap: anywhere; text-align: left; }
        .notificacion-vacia { padding: 18px; color: var(--texto-mutado); text-align: center; font-size: 13px; }

        .dashboard-content {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 30px;
            overflow-x: hidden;
        }
        .header-acciones { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 22px; }
        .header-acciones h2 { margin: 0; color: var(--texto-oscuro); font-size: 28px; }
        .header-acciones p { margin: 6px 0 0; color: var(--texto-mutado); font-size: 14px; }
        .acciones-exportar { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn-exportar { padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; color: white; display: inline-flex; align-items: center; gap: 8px; }
        .btn-exportar:disabled { opacity: 0.7; cursor: not-allowed; }
        .btn-pdf { background-color: #d9534f; }
        .btn-excel { background-color: #5cb85c; }
        .modo-exportacion .acciones-exportar { display: none !important; }

        .seccion-titulo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 16px;
            color: var(--uptex-verde);
            font-size: 20px;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(220px, 100%), 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .kpi-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-left: 5px solid var(--uptex-verde);
            position: relative;
            min-height: 104px;
        }

        .kpi-card h3 { margin: 0 42px 10px 0; font-size: 14px; color: #777; text-transform: uppercase; }
        .kpi-card .valor { font-size: 28px; font-weight: bold; color: var(--texto-oscuro); line-height: 1.2; overflow-wrap: anywhere; }
        .kpi-card .detalle { margin-top: 6px; color: var(--texto-mutado); font-size: 13px; }
        .kpi-card i { font-size: 30px; color: #d9e4de; position: absolute; right: 20px; top: 20px; }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(320px, 100%), 1fr));
            gap: 20px;
        }

        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            min-height: 330px;
        }

        .chart-container h3 {
            margin: 0 0 18px;
            font-size: 16px;
            color: var(--texto-oscuro);
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .chart-box {
            position: relative;
            height: 260px;
        }

        .tab-content { width: 100%; min-width: 0; }
        .tab-content.oculto { display: none; }
        .perfil-grid { display: grid; grid-template-columns: minmax(min(220px, 100%), 280px) minmax(0, 1fr); gap: 24px; }
        .perfil-card { background: white; border-radius: 12px; border: 1px solid var(--borde-color); padding: 24px; box-shadow: 0 10px 28px rgba(0,0,0,0.06); }
        .perfil-avatar { width: 132px; height: 132px; border-radius: 18px; object-fit: cover; background: #e8f0eb; color: var(--uptex-verde); display: flex; align-items: center; justify-content: center; font-size: 46px; margin: 0 auto 16px; border: 1px solid var(--borde-color); overflow: hidden; }
        .perfil-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .form-label-uptex { font-weight: 700; color: var(--texto-oscuro); font-size: 14px; margin-bottom: 8px; display: block; }
        .form-control-uptex { width: 100%; border: 1px solid var(--borde-color); border-radius: 10px; padding: 11px 13px; outline: none; }
        .form-control-uptex:focus { border-color: var(--uptex-verde); box-shadow: 0 0 0 3px rgba(0,104,55,0.12); }
        .btn-guardar { background: var(--uptex-verde); color: white; border: 0; border-radius: 10px; padding: 10px 16px; font-weight: 700; display: inline-flex; gap: 8px; align-items: center; }
        .sidebar-profile { padding: 16px; border-bottom: 1px solid #f0f3f1; display: flex; gap: 12px; align-items: center; }
        .sidebar-profile-icon { width: 40px; height: 40px; border-radius: 10px; background: #e8f0eb; color: var(--uptex-verde); display: flex; align-items: center; justify-content: center; font-weight: 800; overflow: hidden; }
        .sidebar-profile-icon img { width: 100%; height: 100%; object-fit: cover; }
        .sidebar-footer { padding: 16px; border-top: 1px solid #f0f3f1; }
        .sidebar-logout { width: 100%; border: 0; background: transparent; color: var(--uptex-rojo); border-radius: 8px; padding: 10px 12px; display: flex; align-items: center; gap: 12px; font-weight: 700; transition: 0.2s; }
        .sidebar-logout:hover { background: #fdebed; }

        @media (max-width: 760px) {
            body { display: block; }
            .sidebar {
                width: min(82vw, var(--sidebar-width));
                transform: translateX(-100%);
                box-shadow: 0 20px 45px rgba(0,0,0,0.18);
            }
            .sidebar:not(.oculto) { transform: translateX(0); }
            .wrapper { margin-left: 0; width: 100%; }
            .navbar { padding: 12px 16px; gap: 12px; }
            .usuario-info { justify-content: flex-end; gap: 10px; }
            .dashboard-content { padding: 18px 14px; }
            .header-acciones { align-items: flex-start; flex-direction: column; }
            .acciones-exportar { width: 100%; }
            .btn-exportar { flex: 1 1 145px; justify-content: center; }
            .charts-grid { grid-template-columns: 1fr; }
            .perfil-grid { grid-template-columns: 1fr; }
        }

        @media print {
            body { display: block; background: white; color: #111; }
            .sidebar, .navbar, .acciones-exportar { display: none !important; }
            .wrapper { display: block; overflow: visible; }
            .dashboard-content { padding: 0; }
            .kpi-card, .chart-container { box-shadow: none; border: 1px solid #ddd; break-inside: avoid; }
            .charts-grid { grid-template-columns: 1fr; }
            .chart-container { min-height: 300px; page-break-inside: avoid; }
        }
    </style>
</head>
<body>

    <script>
        function validarSesionActiva() {
            const tokenActual = sessionStorage.getItem('token_acceso');
            const rolActual = sessionStorage.getItem('usuario_rol');

            if (!tokenActual || !rolActual || rolActual.toLowerCase() !== 'egresado') {
                window.location.replace('/');
            }
        }

        validarSesionActiva();
        window.addEventListener('pageshow', validarSesionActiva);
    </script>

    <aside class="sidebar oculto" id="sidebar">
        <div class="sidebar-header">Portal Egresado</div>
        <div class="sidebar-profile">
            <div class="sidebar-profile-icon" id="sideAvatar"><i class="fas fa-user-graduate"></i></div>
            <div style="min-width:0;">
                <div id="sideNombre" style="font-weight:800; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Cargando...</div>
                <div style="font-size:12px; color:var(--texto-mutado);">Egresado UPTeX</div>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li><button type="button" class="menu-btn activo" onclick="cambiarPestana('tab-dashboard', this)"><i class="fas fa-chart-line"></i> Dashboard</button></li>
            <li><a href="/vacantes"><i class="fas fa-briefcase"></i> Vacantes</a></li>
            <li><a href="/empresas"><i class="fas fa-building"></i> Empresas</a></li>
            <li><button type="button" class="menu-btn" onclick="cambiarPestana('tab-configuracion', this)"><i class="fas fa-cog"></i> Configuracion</button></li>
        </ul>
        <div class="sidebar-footer">
            <button type="button" class="sidebar-logout" onclick="cerrarSesion()">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesion</span>
            </button>
        </div>
    </aside>

    <div class="wrapper">
        <nav class="navbar">
            <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <div class="usuario-info">
                <div class="notificaciones">
                    <button class="btn-notificaciones" onclick="toggleNotificaciones(event)" title="Notificaciones" aria-label="Notificaciones">
                        <i class="fas fa-bell"></i>
                        <span id="badgeNotificaciones" class="badge-notificaciones">0</span>
                    </button>
                    <div id="panelNotificaciones" class="panel-notificaciones">
                        <header id="panelNotificacionesHeader">
                            <span>Notificaciones</span>
                            <button type="button" onclick="marcarNotificacionesLeidas()">Marcar leidas</button>
                        </header>
                        <div id="listaNotificaciones" class="lista-notificaciones">
                            <div class="notificacion-vacia">Cargando notificaciones...</div>
                        </div>
                    </div>
                </div>
                <span id="nombreDisplay">Cargando...</span>
                <span id="rolDisplay" class="etiqueta-rol">Rol</span>
            </div>
        </nav>

        <main class="dashboard-content">
            <div id="tab-dashboard" class="tab-content">
                <div class="header-acciones">
                    <div>
                        <h2>Dashboard Analitico</h2>
                        <p>Indicadores principales de empleabilidad, contratacion y actividad empresarial.</p>
                    </div>
                    <div class="acciones-exportar">
                        <button id="btnExportarPDF" class="btn-exportar btn-pdf" onclick="exportarPDF()"><i class="fas fa-file-pdf"></i> Exportar PDF</button>
                        <button id="btnExportarExcel" class="btn-exportar btn-excel" onclick="exportarExcel()"><i class="fas fa-file-excel"></i> Exportar Excel</button>
                    </div>
                </div>

                <h3 class="seccion-titulo"><i class="fas fa-gauge-high"></i> Metricas</h3>

                <section class="kpi-grid">
                    <div class="kpi-card">
                        <h3>Tasa de empleabilidad</h3>
                        <div class="valor" id="kpi-empleabilidad">--%</div>
                        <div class="detalle">Egresados contratados sobre total de egresados</div>
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="kpi-card" style="border-left-color: #f0ad4e;">
                        <h3>Salarios promedio</h3>
                        <div class="valor" id="kpi-salario">$ --</div>
                        <div class="detalle">Promedio de salario ofertado en vacantes</div>
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="kpi-card" style="border-left-color: #5bc0de;">
                        <h3>Tiempo promedio de contratacion</h3>
                        <div class="valor" id="kpi-tiempo">-- dias</div>
                        <div class="detalle">Promedio estimado del proceso de contratacion</div>
                        <i class="fas fa-stopwatch"></i>
                    </div>
                    <div class="kpi-card" style="border-left-color: #7b5fb2;">
                        <h3>Empresas mas activas</h3>
                        <div class="valor" id="kpi-empresa-top">--</div>
                        <div class="detalle" id="kpi-empresa-top-detalle">Vacantes activas publicadas</div>
                        <i class="fas fa-building"></i>
                    </div>
                </section>

                <section class="charts-grid">
                    <div class="chart-container">
                        <h3>Carreras con mayor contratacion</h3>
                        <div class="chart-box">
                            <canvas id="graficaCarreras"></canvas>
                        </div>
                    </div>
                    <div class="chart-container">
                        <h3>Empresas mas activas</h3>
                        <div class="chart-box">
                            <canvas id="graficaEmpresas"></canvas>
                        </div>
                    </div>
                    <div class="chart-container">
                        <h3>Vacantes mas solicitadas</h3>
                        <div class="chart-box">
                            <canvas id="graficaVacantes"></canvas>
                        </div>
                    </div>
                </section>
            </div>

            <div id="tab-configuracion" class="tab-content oculto">
                <div class="header-acciones">
                    <div>
                        <h2>Configuracion de Perfil</h2>
                        <p>Actualiza tu nombre, carrera, estado laboral y foto de perfil.</p>
                    </div>
                </div>

                <div class="perfil-grid">
                    <div class="perfil-card" style="text-align:center;">
                        <div class="perfil-avatar" id="perfilAvatar"><i class="fas fa-user-graduate"></i></div>
                        <h3 id="perfilNombrePreview" style="font-size:18px; font-weight:800; margin-bottom:4px;">Egresado</h3>
                        <p id="perfilCorreoPreview" style="color:var(--texto-mutado); font-size:13px; margin:0;">correo</p>
                    </div>
                    <form id="form-perfil-egresado" class="perfil-card">
                        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                            <div>
                                <label class="form-label-uptex" for="perfil_nombre">Nombre</label>
                                <input class="form-control-uptex" type="text" id="perfil_nombre" name="nombre" required>
                            </div>
                            <div>
                                <label class="form-label-uptex" for="perfil_carrera">Carrera</label>
                                <input class="form-control-uptex" type="text" id="perfil_carrera" name="carrera" placeholder="Ej. Ingenieria en Software">
                            </div>
                            <div>
                                <label class="form-label-uptex" for="perfil_estado_laboral">Estado laboral</label>
                                <select class="form-control-uptex" id="perfil_estado_laboral" name="estado_laboral">
                                    <option value="">Sin especificar</option>
                                    <option value="buscando">Buscando empleo</option>
                                    <option value="contratado">Contratado</option>
                                    <option value="practicas">Practicas/Estadias</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label-uptex" for="perfil_foto">Foto de perfil</label>
                                <input class="form-control-uptex" type="file" id="perfil_foto" name="foto" accept="image/png,image/jpeg,image/webp">
                            </div>
                        </div>
                        <div style="margin-top:18px; display:flex; justify-content:flex-end;">
                            <button type="submit" class="btn-guardar" id="btnGuardarPerfil"><i class="fas fa-save"></i> Guardar Perfil</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        let datosDashboardActuales = null;
        let notificacionesActuales = [];
        let notificacionesLeidas = sessionStorage.getItem('egresado_notificaciones_leidas') === '1';

        document.addEventListener('DOMContentLoaded', function() {
            ajustarSidebarInicial();
            document.getElementById('nombreDisplay').textContent = sessionStorage.getItem('usuario_nombre') || 'Usuario';
            document.getElementById('rolDisplay').textContent = sessionStorage.getItem('usuario_rol') || 'Perfil';
            document.getElementById('sideNombre').textContent = sessionStorage.getItem('usuario_nombre') || 'Usuario';
            cargarDatosReales();
            cargarNotificaciones();
            cargarPerfilEgresado();
            document.getElementById('form-perfil-egresado')?.addEventListener('submit', guardarPerfilEgresado);
            habilitarArrastrePanelNotificaciones('panelNotificaciones', 'panelNotificacionesHeader');
        });

        window.addEventListener('resize', ajustarSidebarInicial);

        function ajustarSidebarInicial() {
            const sidebar = document.getElementById('sidebar');
            if (!sidebar) return;

            if (window.innerWidth > 760) {
                sidebar.classList.remove('oculto');
            } else {
                sidebar.classList.add('oculto');
            }
        }

        document.addEventListener('click', function(event) {
            const contenedor = document.querySelector('.notificaciones');
            if (contenedor && !contenedor.contains(event.target)) {
                const panel = document.getElementById('panelNotificaciones');
                if (panel?.classList.contains('activo')) {
                    guardarPosicionPanelNotificaciones(panel, 'egresado_panel_notificaciones_posicion');
                    panel.classList.remove('activo');
                }
            }
        });

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('oculto');
        }

        function cambiarPestana(tabId, boton) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('oculto'));
            document.getElementById(tabId)?.classList.remove('oculto');

            document.querySelectorAll('.menu-btn').forEach(item => item.classList.remove('activo'));
            boton?.classList.add('activo');
        }

        async function cerrarSesion() {
            const token = sessionStorage.getItem('token_acceso');

            try {
                if (token) {
                    await fetch('/api/logout', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                }
            } finally {
                sessionStorage.clear();
                localStorage.clear();
                window.location.replace('/');
            }
        }

        async function cargarDatosReales() {
            try {
                const token = sessionStorage.getItem('token_acceso');
                const respuesta = await fetch('/api/dashboard/metricas', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                const datos = await respuesta.json();
                datosDashboardActuales = datos;

                document.getElementById('kpi-empleabilidad').textContent = datos.kpis.empleabilidad + '%';
                document.getElementById('kpi-tiempo').textContent = datos.kpis.tiempo_promedio + ' dias';
                document.getElementById('kpi-salario').textContent = '$ ' + datos.kpis.salario_promedio;
                document.getElementById('kpi-empresa-top').textContent = datos.kpis.empresa_top_nombre || 'Sin datos';
                document.getElementById('kpi-empresa-top-detalle').textContent =
                    (datos.kpis.empresa_top_total || 0) + ' vacantes activas publicadas';

                if (datos.exito === false) {
                    console.warn(datos.mensaje);
                }

                dibujarGraficas(datos.graficas);
                renderNotificaciones();
            } catch (error) {
                console.error('Error al obtener los datos del dashboard:', error);
                dibujarGraficas({
                    carreras: { etiquetas: ['Sin datos'], valores: [0] },
                    empresas: { etiquetas: ['Sin datos'], valores: [0] },
                    vacantes: { etiquetas: ['Sin datos'], valores: [0] }
                });
            }
        }

        async function cargarNotificaciones() {
            try {
                const respuesta = await fetch('/api/vacantes', {
                    headers: { 'Accept': 'application/json' }
                });
                const vacantes = await respuesta.json();
                const listaVacantes = Array.isArray(vacantes) ? vacantes : (vacantes.data || []);

                notificacionesActuales = listaVacantes.slice(0, 4).map(vacante => ({
                    titulo: vacante.puesto || vacante.titulo || 'Nueva vacante disponible',
                    detalle: `${vacante.empresa || 'Empresa'} - ${vacante.ubicacion || vacante.tipo_jornada || 'Modalidad no especificada'}`,
                    icono: 'fa-briefcase'
                }));
                if (notificacionesActuales.length > 0 && sessionStorage.getItem('egresado_notificaciones_total') !== String(notificacionesActuales.length)) {
                    notificacionesLeidas = false;
                    sessionStorage.setItem('egresado_notificaciones_leidas', '0');
                    sessionStorage.setItem('egresado_notificaciones_total', String(notificacionesActuales.length));
                }

                renderNotificaciones();
            } catch (error) {
                notificacionesActuales = [];
                renderNotificaciones();
            }
        }

        function renderNotificaciones() {
            const lista = document.getElementById('listaNotificaciones');
            const badge = document.getElementById('badgeNotificaciones');
            if (!lista || !badge) return;

            const notificaciones = [...notificacionesActuales];

            if (!notificaciones.length) {
                lista.innerHTML = '<div class="notificacion-vacia">No hay notificaciones nuevas.</div>';
                badge.style.display = 'none';
                return;
            }

            if (notificacionesLeidas) {
                badge.style.display = 'none';
            } else {
                badge.textContent = notificaciones.length > 9 ? '9+' : notificaciones.length;
                badge.style.display = 'inline-flex';
            }
            lista.innerHTML = notificaciones.map(item => `
                <div class="notificacion-item">
                    <span class="notificacion-icono"><i class="fas ${escaparHtml(item.icono)}"></i></span>
                    <div class="notificacion-texto">
                        <strong>${escaparHtml(item.titulo)}</strong>
                        <span>${escaparHtml(item.detalle)}</span>
                    </div>
                </div>
            `).join('');
        }

        function toggleNotificaciones(event) {
            event.stopPropagation();
            const panel = document.getElementById('panelNotificaciones');
            const boton = event.currentTarget;
            const seAbrira = !panel.classList.contains('activo');

            if (seAbrira) {
                panel.classList.add('activo');
                requestAnimationFrame(() => {
                    restaurarOPosicionarPanelNotificaciones(panel, boton, 'egresado_panel_notificaciones_posicion');
                });
            } else {
                guardarPosicionPanelNotificaciones(panel, 'egresado_panel_notificaciones_posicion');
                panel.classList.remove('activo');
            }
        }

        function restaurarOPosicionarPanelNotificaciones(panel, boton, storageKey) {
            let posicionGuardada = null;
            try {
                posicionGuardada = JSON.parse(localStorage.getItem(storageKey) || 'null');
            } catch (error) {
                localStorage.removeItem(storageKey);
            }

            if (posicionGuardada && Number.isFinite(posicionGuardada.left) && Number.isFinite(posicionGuardada.top)) {
                panel.style.position = 'fixed';
                if (posicionGuardada.width) panel.style.width = `${Math.min(posicionGuardada.width, window.innerWidth - 24)}px`;
                if (posicionGuardada.height) panel.style.height = `${Math.min(posicionGuardada.height, window.innerHeight - 24)}px`;
                panel.style.left = `${posicionGuardada.left}px`;
                panel.style.top = `${posicionGuardada.top}px`;
                panel.style.right = 'auto';
                panel.style.maxHeight = 'calc(100vh - 24px)';
                limitarPanelNotificacionesAlViewport(panel);
                return;
            }

            posicionarPanelNotificaciones(panel, boton);
        }

        function posicionarPanelNotificaciones(panel, boton) {
            const rect = boton.getBoundingClientRect();
            const ancho = Math.min(352, window.innerWidth - 24);
            const top = Math.max(12, Math.min(rect.bottom + 10, window.innerHeight - 160));
            const altoMaximo = Math.max(180, window.innerHeight - top - 12);

            panel.style.position = 'fixed';
            panel.style.width = `${ancho}px`;
            panel.style.top = `${top}px`;
            panel.style.left = `${Math.max(12, Math.min(window.innerWidth - ancho - 12, rect.right - ancho))}px`;
            panel.style.right = 'auto';
            panel.style.maxHeight = `${altoMaximo}px`;
        }

        window.addEventListener('resize', () => {
            const panel = document.getElementById('panelNotificaciones');
            if (panel?.classList.contains('activo')) limitarPanelNotificacionesAlViewport(panel);
        });

        function limitarPanelNotificacionesAlViewport(panel) {
            const rect = panel.getBoundingClientRect();
            const ancho = panel.offsetWidth || rect.width;
            const alto = panel.offsetHeight || rect.height;
            const nuevoX = Math.max(12, Math.min(window.innerWidth - ancho - 12, rect.left));
            const nuevoY = Math.max(12, Math.min(window.innerHeight - alto - 12, rect.top));

            panel.style.left = `${nuevoX}px`;
            panel.style.top = `${nuevoY}px`;
            panel.style.right = 'auto';
        }

        function habilitarArrastrePanelNotificaciones(panelId, headerId) {
            const panel = document.getElementById(panelId);
            const header = document.getElementById(headerId);
            if (!panel || !header) return;

            let inicioX = 0;
            let inicioY = 0;
            let panelX = 0;
            let panelY = 0;

            header.addEventListener('pointerdown', (event) => {
                if (event.target.closest('button')) return;

                const rect = panel.getBoundingClientRect();
                inicioX = event.clientX;
                inicioY = event.clientY;
                panelX = rect.left;
                panelY = rect.top;
                panel.style.position = 'fixed';
                panel.style.left = `${panelX}px`;
                panel.style.top = `${panelY}px`;
                panel.style.right = 'auto';
                header.setPointerCapture(event.pointerId);
                panel.style.transition = 'none';
                event.preventDefault();
            });

            header.addEventListener('pointermove', (event) => {
                if (!header.hasPointerCapture(event.pointerId)) return;

                const ancho = panel.offsetWidth;
                const alto = panel.offsetHeight;
                const nuevoX = Math.max(12, Math.min(window.innerWidth - ancho - 12, panelX + event.clientX - inicioX));
                const nuevoY = Math.max(12, Math.min(window.innerHeight - alto - 12, panelY + event.clientY - inicioY));

                panel.style.left = `${nuevoX}px`;
                panel.style.top = `${nuevoY}px`;
                panel.style.right = 'auto';
            });

            header.addEventListener('pointerup', (event) => {
                if (header.hasPointerCapture(event.pointerId)) {
                    header.releasePointerCapture(event.pointerId);
                    guardarPosicionPanelNotificaciones(panel, 'egresado_panel_notificaciones_posicion');
                }
            });

            header.addEventListener('pointercancel', (event) => {
                if (header.hasPointerCapture(event.pointerId)) {
                    header.releasePointerCapture(event.pointerId);
                    guardarPosicionPanelNotificaciones(panel, 'egresado_panel_notificaciones_posicion');
                }
            });
        }

        function guardarPosicionPanelNotificaciones(panel, storageKey) {
            const rect = panel.getBoundingClientRect();
            localStorage.setItem(storageKey, JSON.stringify({
                left: rect.left,
                top: rect.top,
                width: rect.width,
                height: rect.height
            }));
        }

        function marcarNotificacionesLeidas() {
            notificacionesLeidas = true;
            sessionStorage.setItem('egresado_notificaciones_leidas', '1');
            document.getElementById('badgeNotificaciones').style.display = 'none';
        }

        async function exportarPDF() {
            if (!datosDashboardActuales) {
                alert('Espera a que carguen los datos del dashboard.');
                return;
            }

            const boton = document.getElementById('btnExportarPDF');
            const textoOriginal = boton.innerHTML;
            boton.disabled = true;
            boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando PDF';

            try {
                if (!window.html2canvas || !window.jspdf?.jsPDF) {
                    throw new Error('No se cargaron las librerias para generar PDF.');
                }

                document.body.classList.add('modo-exportacion');
                const contenido = document.querySelector('#tab-dashboard');
                const canvas = await html2canvas(contenido, {
                    scale: 2,
                    backgroundColor: '#f4f7f6',
                    useCORS: true
                });

                const imagen = canvas.toDataURL('image/png');
                const pdf = new window.jspdf.jsPDF('p', 'mm', 'a4');
                const margen = 10;
                const anchoPagina = pdf.internal.pageSize.getWidth();
                const altoPagina = pdf.internal.pageSize.getHeight();
                const anchoImagen = anchoPagina - (margen * 2);
                const altoImagen = (canvas.height * anchoImagen) / canvas.width;
                let altoRestante = altoImagen;
                let posicion = margen;

                pdf.addImage(imagen, 'PNG', margen, posicion, anchoImagen, altoImagen);
                altoRestante -= (altoPagina - margen * 2);

                while (altoRestante > 0) {
                    posicion = altoRestante - altoImagen + margen;
                    pdf.addPage();
                    pdf.addImage(imagen, 'PNG', margen, posicion, anchoImagen, altoImagen);
                    altoRestante -= (altoPagina - margen * 2);
                }

                pdf.save(`dashboard-analitico-${fechaArchivo()}.pdf`);
            } catch (error) {
                console.error('Error al exportar PDF:', error);
                alert('No se pudo generar el PDF. Verifica tu conexion e intenta de nuevo.');
            } finally {
                document.body.classList.remove('modo-exportacion');
                boton.disabled = false;
                boton.innerHTML = textoOriginal;
            }
        }

        function exportarExcel() {
            if (!datosDashboardActuales) {
                alert('Espera a que carguen los datos del dashboard.');
                return;
            }

            const boton = document.getElementById('btnExportarExcel');
            const textoOriginal = boton.innerHTML;
            boton.disabled = true;
            boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando Excel';

            const htmlExcel = `
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        table { border-collapse: collapse; font-family: Arial, sans-serif; margin-bottom: 18px; }
                        th { background: #006837; color: #ffffff; font-weight: bold; }
                        th, td { border: 1px solid #d9d9d9; padding: 8px; }
                        h1, h2 { font-family: Arial, sans-serif; color: #006837; }
                    </style>
                </head>
                <body>
                    <h1>Dashboard Analitico UPTeX</h1>
                    <p>Generado: ${escaparHtml(new Date().toLocaleString('es-MX'))}</p>
                    ${tablaExcel('Metricas', [
                        ['Metrica', 'Valor'],
                        ['Tasa de empleabilidad', `${datosDashboardActuales.kpis?.empleabilidad ?? 0}%`],
                        ['Salario promedio', datosDashboardActuales.kpis?.salario_promedio ?? 0],
                        ['Tiempo promedio de contratacion', `${datosDashboardActuales.kpis?.tiempo_promedio ?? 0} dias`],
                        ['Empresa mas activa', datosDashboardActuales.kpis?.empresa_top_nombre || 'Sin datos'],
                        ['Vacantes de la empresa mas activa', datosDashboardActuales.kpis?.empresa_top_total ?? 0]
                    ])}
                    ${tablaExcel('Carreras con mayor contratacion', [['Carrera', 'Total'], ...filasGrafica(datosDashboardActuales.graficas?.carreras)])}
                    ${tablaExcel('Empresas mas activas', [['Empresa', 'Total'], ...filasGrafica(datosDashboardActuales.graficas?.empresas)])}
                    ${tablaExcel('Vacantes mas solicitadas', [['Vacante', 'Total'], ...filasGrafica(datosDashboardActuales.graficas?.vacantes)])}
                </body>
                </html>
            `;

            const blob = new Blob(['\ufeff' + htmlExcel], { type: 'application/vnd.ms-excel;charset=utf-8;' });
            const enlace = document.createElement('a');
            enlace.href = URL.createObjectURL(blob);
            enlace.download = `dashboard-analitico-${fechaArchivo()}.xls`;
            document.body.appendChild(enlace);
            enlace.click();
            document.body.removeChild(enlace);
            URL.revokeObjectURL(enlace.href);
            boton.disabled = false;
            boton.innerHTML = textoOriginal;
        }

        function filasGrafica(datos) {
            const grafica = normalizarGrafica(datos);
            return grafica.etiquetas.map((etiqueta, index) => [etiqueta, grafica.valores[index] ?? 0]);
        }

        async function cargarPerfilEgresado() {
            const token = sessionStorage.getItem('token_acceso');

            try {
                const respuesta = await fetch('/api/egresado/perfil', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!respuesta.ok) return;

                const perfil = await respuesta.json();
                document.getElementById('perfil_nombre').value = perfil.nombre || '';
                document.getElementById('perfil_carrera').value = perfil.carrera || '';
                document.getElementById('perfil_estado_laboral').value = perfil.estado_laboral || '';
                document.getElementById('perfilNombrePreview').textContent = perfil.nombre || 'Egresado';
                document.getElementById('perfilCorreoPreview').textContent = perfil.correo || '';
                document.getElementById('nombreDisplay').textContent = perfil.nombre || 'Usuario';
                document.getElementById('sideNombre').textContent = perfil.nombre || 'Usuario';
                actualizarAvatar(perfil.foto_url);
            } catch (error) {
                console.error('No se pudo cargar el perfil:', error);
            }
        }

        async function guardarPerfilEgresado(event) {
            event.preventDefault();
            const token = sessionStorage.getItem('token_acceso');
            const boton = document.getElementById('btnGuardarPerfil');
            const textoOriginal = boton.innerHTML;
            const formData = new FormData(event.target);

            boton.disabled = true;
            boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando';

            try {
                const respuesta = await fetch('/api/egresado/perfil', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await respuesta.json();
                if (!respuesta.ok) {
                    throw new Error(data.mensaje || data.message || 'No se pudo actualizar el perfil.');
                }

                sessionStorage.setItem('usuario_nombre', data.usuario.nombre);
                document.getElementById('nombreDisplay').textContent = data.usuario.nombre;
                document.getElementById('sideNombre').textContent = data.usuario.nombre;
                document.getElementById('perfilNombrePreview').textContent = data.usuario.nombre;
                actualizarAvatar(data.usuario.foto_url);
                alert('Perfil actualizado correctamente.');
            } catch (error) {
                alert(error.message || 'Error al guardar el perfil.');
            } finally {
                boton.disabled = false;
                boton.innerHTML = textoOriginal;
            }
        }

        function actualizarAvatar(url) {
            const avatarHtml = url
                ? `<img src="${escaparHtml(url)}" alt="Foto de perfil">`
                : '<i class="fas fa-user-graduate"></i>';

            document.getElementById('perfilAvatar').innerHTML = avatarHtml;
            document.getElementById('sideAvatar').innerHTML = avatarHtml;
        }

        function tablaExcel(titulo, filas) {
            const encabezado = filas[0] || [];
            const cuerpo = filas.slice(1);
            return `
                <h2>${escaparHtml(titulo)}</h2>
                <table>
                    <thead><tr>${encabezado.map(celda => `<th>${escaparHtml(celda)}</th>`).join('')}</tr></thead>
                    <tbody>
                        ${cuerpo.map(fila => `<tr>${fila.map(celda => `<td>${escaparHtml(celda)}</td>`).join('')}</tr>`).join('')}
                    </tbody>
                </table>
            `;
        }

        function fechaArchivo() {
            return new Date().toISOString().slice(0, 10);
        }

        function escaparHtml(valor) {
            return String(valor ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function normalizarGrafica(datos) {
            if (!datos || !datos.etiquetas || datos.etiquetas.length === 0) {
                return { etiquetas: ['Sin datos'], valores: [0] };
            }

            return {
                etiquetas: datos.etiquetas.map(valor => valor || 'Sin especificar'),
                valores: datos.valores.length > 0 ? datos.valores : [0]
            };
        }

        function crearGrafica(canvasId, tipo, datos, colores) {
            const grafica = normalizarGrafica(datos);
            const contexto = document.getElementById(canvasId).getContext('2d');
            Chart.defaults.font.family = 'Inter, Segoe UI, sans-serif';
            Chart.defaults.color = '#68746f';

            new Chart(contexto, {
                type: tipo,
                data: {
                    labels: grafica.etiquetas,
                    datasets: [{
                        label: 'Total',
                        data: grafica.valores,
                        backgroundColor: colores,
                        borderColor: tipo === 'doughnut' ? '#ffffff' : colores,
                        borderRadius: tipo === 'bar' ? 12 : 0,
                        borderWidth: tipo === 'doughnut' ? 3 : 0,
                        barPercentage: 0.62,
                        categoryPercentage: 0.72,
                        hoverOffset: tipo === 'doughnut' ? 8 : 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 900, easing: 'easeOutQuart' },
                    plugins: {
                        legend: {
                            display: tipo !== 'bar',
                            position: 'bottom',
                            labels: { usePointStyle: true, boxWidth: 8, padding: 18 }
                        },
                        tooltip: {
                            backgroundColor: '#24342d',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: false
                        }
                    },
                    scales: tipo === 'bar' ? {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                            grid: { color: 'rgba(104,116,111,0.14)', drawBorder: false },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            border: { display: false }
                        }
                    } : {}
                }
            });
        }

        function dibujarGraficas(graficasData) {
            crearGrafica('graficaCarreras', 'bar', graficasData.carreras, '#006837');
            crearGrafica('graficaEmpresas', 'bar', graficasData.empresas, '#5bc0de');
            crearGrafica('graficaVacantes', 'doughnut', graficasData.vacantes, ['#006837', '#85b998', '#C1272D', '#d97e81', '#1a2226']);
        }
    </script>
</body>
</html>



