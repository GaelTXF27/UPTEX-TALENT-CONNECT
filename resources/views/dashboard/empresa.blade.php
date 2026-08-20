<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPTeX - Portal de Empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/uptex-bootstrap-theme.css') }}">

    <!-- FONT AWESOME PARA ICONOS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CHART.JS PARA GRAFICAS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>

    <!-- TAILWIND CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        uptex: {
                            green: '#006837',
                            lightgreen: '#e8f0eb',
                            red: '#C1272D',
                            dark: '#333333',
                            gray: '#f4f7f5'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'Segoe UI', 'Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Ocultar barra de desplazamiento manteniendo funcionalidad */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Transiciones suaves para la sidebar y modales */
        .sidebar-transition { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .fade-in { animation: fadeIn 0.4s ease-out forwards; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Estilos para el Toast (Notificaciones) */
        .toast-enter { animation: toastEnter 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
        .toast-leave { animation: toastLeave 0.3s ease-in forwards; }
        
        @keyframes toastEnter {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes toastLeave {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        /* Loading overlay para la simulacion de datos reales */
        .loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(2px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
            transition: opacity 0.3s;
        }

        .panel-notificaciones-flotante {
            position: fixed !important;
            width: min(352px, calc(100vw - 24px));
            min-width: 260px;
            min-height: 170px;
            max-width: calc(100vw - 24px);
            max-height: calc(100vh - 24px);
            overflow: auto;
            resize: both;
            z-index: 5000;
        }

        #empresa-panel-notificaciones:not(.hidden) {
            display: flex;
            flex-direction: column;
        }

        #empresa-panel-notificaciones::after {
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

        .notificaciones-header-arrastrable {
            cursor: default;
            user-select: none;
        }

        #empresa-lista-notificaciones {
            flex: 1 1 auto;
            min-height: 100px;
            overflow-y: auto;
        }
    </style>

    <script>
        function validarSesionEmpresa() {
            const tokenActual = sessionStorage.getItem('token_acceso');
            const rolActual = sessionStorage.getItem('usuario_rol');

            if (!tokenActual || !rolActual || rolActual.toLowerCase() !== 'empresa') {
                sessionStorage.clear();
                localStorage.clear();
                window.location.replace('/');
            }
        }

        validarSesionEmpresa();
        window.addEventListener('pageshow', validarSesionEmpresa);
    </script>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden text-gray-800">

    <aside id="sidebar" class="sidebar-transition w-64 bg-white border-r border-gray-200 flex flex-col z-40 relative group shadow-sm">
        <div class="h-16 bg-uptex-green text-white flex items-center justify-between px-4">
            <h2 id="sidebar-title" class="font-bold text-lg whitespace-nowrap overflow-hidden transition-opacity duration-300">Portal Empresa</h2>
            <button onclick="toggleSidebar()" class="text-white hover:bg-white/20 p-2 rounded-md transition-colors">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <div class="p-4 border-b border-gray-100 flex items-center gap-3 overflow-hidden">
            <div class="w-10 h-10 rounded-lg bg-uptex-lightgreen text-uptex-green flex items-center justify-center font-bold shrink-0">
                <i class="fas fa-building"></i>
            </div>
            <div class="menu-text whitespace-nowrap">
                <p class="text-sm font-bold text-gray-800 truncate" id="side-empresa-nombre">Cargando...</p>
                <p class="text-xs text-gray-500 truncate" id="side-rep-nombre">Representante</p>
            </div>
        </div>

        <ul class="flex-1 py-4 space-y-1 overflow-y-auto no-scrollbar">
            <li>
                <button onclick="cambiarPestana('tab-dashboard', this)" class="menu-btn w-full flex items-center px-4 py-3 text-sm font-medium transition-colors bg-uptex-lightgreen text-uptex-green border-r-4 border-uptex-green">
                    <i class="fas fa-chart-line w-6 text-center text-lg"></i>
                    <span class="menu-text ml-3 whitespace-nowrap">Dashboard</span>
                </button>
            </li>
            <li>
                <button onclick="cambiarPestana('tab-vacantes', this)" class="menu-btn w-full flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-uptex-green border-r-4 border-transparent transition-colors">
                    <i class="fas fa-briefcase w-6 text-center text-lg"></i>
                    <span class="menu-text ml-3 whitespace-nowrap">Mis Vacantes</span>
                </button>
            </li>
            <li>
                <button onclick="cambiarPestana('tab-solicitantes', this)" class="menu-btn w-full flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-uptex-green border-r-4 border-transparent transition-colors">
                    <i class="fas fa-users w-6 text-center text-lg"></i>
                    <span class="menu-text ml-3 whitespace-nowrap">Solicitantes</span>
                </button>
            </li>
            <li>
                <button onclick="cambiarPestana('tab-perfil', this)" class="menu-btn w-full flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-uptex-green border-r-4 border-transparent transition-colors">
                    <i class="fas fa-id-card w-6 text-center text-lg"></i>
                    <span class="menu-text ml-3 whitespace-nowrap">Datos de la Empresa</span>
                </button>
            </li>
        </ul>
        
        <div class="p-4 border-t border-gray-200">
            <button onclick="cerrarSesion()" class="w-full flex items-center px-2 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-md transition-colors">
                <i class="fas fa-sign-out-alt w-6 text-center text-lg"></i>
                <span class="menu-text ml-3 whitespace-nowrap">Cerrar Sesion</span>
            </button>
        </div>
    </aside>

    <main class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 shadow-sm z-30">
            <div class="flex items-center">
                <h1 class="text-xl font-bold text-gray-800 hidden sm:block">Reclutamiento Universitario <span class="text-uptex-green">- UPTeX</span></h1>
                <h1 class="text-lg font-bold text-uptex-green sm:hidden">Portal Empresas</h1>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative" id="empresa-notificaciones">
                    <button onclick="toggleNotificacionesEmpresa(event)" class="text-gray-400 hover:text-uptex-green transition-colors relative w-9 h-9 rounded-full hover:bg-uptex-lightgreen" title="Notificaciones" aria-label="Notificaciones">
                        <i class="fas fa-bell text-xl"></i>
                        <span id="empresa-badge-notificaciones" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold min-w-4 h-4 px-1 rounded-full items-center justify-center">0</span>
                    </button>
                    <div id="empresa-panel-notificaciones" class="hidden panel-notificaciones-flotante bg-white border border-gray-200 rounded-lg shadow-xl">
                        <div id="empresa-panel-notificaciones-header" class="notificaciones-header-arrastrable px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between gap-3">
                            <span class="text-sm font-bold text-gray-800 min-w-0">Notificaciones</span>
                            <button type="button" onclick="marcarNotificacionesEmpresaLeidas()" class="text-xs font-bold text-uptex-green hover:underline whitespace-nowrap">Marcar leidas</button>
                        </div>
                        <div id="empresa-lista-notificaciones">
                            <div class="px-4 py-5 text-sm text-gray-500 text-center">Cargando notificaciones...</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-auto p-4 sm:p-6 lg:p-8 bg-gray-50/50 relative">
            <div class="max-w-7xl mx-auto">
                
                <!-- ================= PESTANA 1: DASHBOARD ================= -->
                <div id="tab-dashboard" class="tab-content block relative">
                    <!-- Overlay de carga simulando peticion real -->
                    <div id="dashboard-loading" class="loading-overlay rounded-xl">
                        <div class="flex flex-col items-center text-uptex-green">
                            <i class="fas fa-circle-notch fa-spin text-4xl mb-2"></i>
                            <span class="font-semibold">Obteniendo datos en tiempo real...</span>
                        </div>
                    </div>

                    <div class="fade-in" id="dashboard-content">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 pb-4 border-b border-gray-200">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">Rendimiento de Reclutamiento</h2>
                                <p class="text-sm text-gray-500 mt-1">Estadisticas en tiempo real de tus vacantes y procesos activos.</p>
                            </div>
                            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                                <button id="btnExportarPDFEmpresa" onclick="exportarPDFEmpresa()" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-uptex-red hover:bg-red-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                    <i class="fas fa-file-pdf"></i> Exportar PDF
                                </button>
                                <button id="btnExportarExcelEmpresa" onclick="exportarExcelEmpresa()" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                </button>
                                <button onclick="actualizarDashboard()" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg shadow-sm transition-colors">
                                    <i class="fas fa-sync-alt text-uptex-green"></i> Actualizar
                                </button>
                            </div>
                        </div>

                        <!-- KPIs -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Vacantes Activas</p>
                                        <h3 class="text-3xl font-extrabold text-gray-800 mt-1" id="kpi-vacantes">0</h3>
                                    </div>
                                    <div class="w-10 h-10 rounded-full bg-uptex-lightgreen flex items-center justify-center text-uptex-green"><i class="fas fa-briefcase"></i></div>
                                </div>
                                <p class="text-xs text-green-600 font-medium flex items-center gap-1"><i class="fas fa-arrow-up"></i> 2 esta semana</p>
                            </div>
                            
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Postulantes</p>
                                        <h3 class="text-3xl font-extrabold text-gray-800 mt-1" id="kpi-postulantes">0</h3>
                                    </div>
                                    <div class="w-10 h-10 rounded-full bg-uptex-lightgreen flex items-center justify-center text-uptex-green"><i class="fas fa-users"></i></div>
                                </div>
                                <p class="text-xs text-green-600 font-medium flex items-center gap-1"><i class="fas fa-arrow-up"></i> +15% vs mes anterior</p>
                            </div>

                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tasa de Contratacion</p>
                                        <h3 class="text-3xl font-extrabold text-gray-800 mt-1" id="kpi-tasa">0%</h3>
                                    </div>
                                    <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-green-600"><i class="fas fa-check-circle"></i></div>
                                </div>
                                <p class="text-xs text-gray-500 font-medium">De los entrevistados</p>
                            </div>

                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Visitas a Perfil</p>
                                        <h3 class="text-3xl font-extrabold text-gray-800 mt-1" id="kpi-visitas">0</h3>
                                    </div>
                                    <div class="w-10 h-10 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-500"><i class="fas fa-eye"></i></div>
                                </div>
                                <p class="text-xs text-gray-500 font-medium">Alumnos interesados</p>
                            </div>
                        </div>

                        <!-- Graficas adaptadas para Empresa -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center justify-between">
                                    Perfiles Demandados <span class="text-xs font-normal text-gray-400">(Por Carrera)</span>
                                </h3>
                                <div class="relative h-64 w-full">
                                    <canvas id="graficaPerfiles"></canvas>
                                </div>
                            </div>
                            
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 xl:col-span-2">
                                <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center justify-between">
                                    Actividad de Postulaciones <span class="text-xs font-normal text-gray-400">(Ultimos 6 meses)</span>
                                </h3>
                                <div class="relative h-64 w-full">
                                    <canvas id="graficaEvolucion"></canvas>
                                </div>
                            </div>
                            
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2 xl:col-span-3">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-sm font-bold text-gray-700">Estado Actual del Embudo de Contratacion</h3>
                                </div>
                                <div class="flex flex-col md:flex-row items-center gap-8">
                                    <div class="relative h-48 w-full md:w-1/3 flex-shrink-0">
                                        <canvas id="graficaEmbudo"></canvas>
                                    </div>
                                    <div class="w-full grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div class="p-4 bg-gray-50 rounded-lg text-center border-t-4 border-gray-400">
                                            <p class="text-xs text-gray-500 font-bold uppercase mb-1">Pendientes</p>
                                            <p class="text-2xl font-black text-gray-800" id="funnel-1">0</p>
                                        </div>
                                        <div class="p-4 bg-uptex-lightgreen rounded-lg text-center border-t-4 border-uptex-green">
                                            <p class="text-xs text-gray-500 font-bold uppercase mb-1">En Entrevista</p>
                                            <p class="text-2xl font-black text-gray-800" id="funnel-2">0</p>
                                        </div>
                                        <div class="p-4 bg-red-50 rounded-lg text-center border-t-4 border-red-500">
                                            <p class="text-xs text-gray-500 font-bold uppercase mb-1">Rechazados</p>
                                            <p class="text-2xl font-black text-gray-800" id="funnel-3">0</p>
                                        </div>
                                        <div class="p-4 bg-green-50 rounded-lg text-center border-t-4 border-green-500">
                                            <p class="text-xs text-gray-500 font-bold uppercase mb-1">Contratados</p>
                                            <p class="text-2xl font-black text-gray-800" id="funnel-4">0</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= PESTANA 2: VACANTES ================= -->
                <div id="tab-vacantes" class="tab-content hidden fade-in">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Mis Vacantes</h2>
                            <p class="text-sm text-gray-500 mt-1">Gestiona los puestos de trabajo publicados por tu empresa.</p>
                        </div>
                        <button onclick="openModalNuevaVacante()" class="px-5 py-2.5 bg-uptex-green hover:bg-green-800 text-white font-medium rounded-lg shadow-md transition-colors flex items-center gap-2">
                            <i class="fas fa-plus"></i> <span class="hidden sm:inline">Publicar Vacante</span>
                        </button>
                    </div>

                    <!-- Contenedor Grid de Vacantes -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="lista-vacantes">
                        <!-- Las tarjetas se inyectan via JS -->
                    </div>
                </div>

                <!-- ================= PESTANA 3: SOLICITANTES ================= -->
                <div id="tab-solicitantes" class="tab-content hidden fade-in">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Alumnos y Egresados Postulados</h2>
                                <p class="text-sm text-gray-500 mt-1">Revisa el talento de UPTeX interesado en tus vacantes.</p>
                            </div>
                            <div class="flex gap-3 w-full md:w-auto">
                                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none text-sm bg-white flex-1 md:flex-none">
                                    <option value="todas">Todas las vacantes</option>
                                    <option value="v1">Desarrollador Full Stack</option>
                                    <option value="v2">Ingeniero de Procesos</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                                        <th class="py-4 px-6 font-semibold">Candidato</th>
                                        <th class="py-4 px-6 font-semibold">Carrera</th>
                                        <th class="py-4 px-6 font-semibold">Vacante Aplicada</th>
                                        <th class="py-4 px-6 font-semibold">Fecha</th>
                                        <th class="py-4 px-6 font-semibold">Estado</th>
                                        <th class="py-4 px-6 font-semibold text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-solicitantes-body" class="text-sm divide-y divide-gray-100 bg-white">
                                    <!-- Contenido dinamico -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ================= PESTANA 4: PERFIL EMPRESA ================= -->
                <div id="tab-perfil" class="tab-content hidden fade-in">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-4xl mx-auto">
                        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/50">
                            <h2 class="text-xl font-bold text-gray-800">Perfil Institucional de la Empresa</h2>
                            <p class="text-sm text-gray-500 mt-1">Esta informacion sera visible para los alumnos al buscar vacantes.</p>
                        </div>
                        
                        <form id="form-perfil-empresa" class="p-6 md:p-8">
                            <div class="flex flex-col md:flex-row gap-8 mb-8">
                                <div class="w-full md:w-1/3 flex flex-col items-center">
                                    <div class="w-32 h-32 rounded-xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 mb-4 relative overflow-hidden group cursor-pointer hover:border-uptex-green transition-colors">
                                        <i class="fas fa-building text-4xl group-hover:hidden"></i>
                                        <div class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center text-white text-sm font-medium">
                                            Subir Logo
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 text-center">Formato PNG o JPG<br>Max. 2MB</p>
                                </div>
                                
                                <div class="w-full md:w-2/3 grid grid-cols-1 gap-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Razon Social / Nombre Comercial</label>
                                        <input type="text" id="perfil-nombre" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none bg-gray-50" readonly>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Sector Industrial</label>
                                            <select id="perfil-sector" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none bg-white">
                                                <option>Tecnologias de la Informacion</option>
                                                <option>Manufactura y Logistica</option>
                                                <option>Finanzas y Negocios</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Sitio Web</label>
                                            <input type="url" id="perfil-web" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="border-gray-100 mb-6">
                            
                            <h3 class="text-md font-bold text-gray-800 mb-4">Informacion de Contacto (Reclutamiento)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre del Enlace de RH</label>
                                    <input type="text" id="perfil-representante" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none bg-gray-50" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Correo para CVs</label>
                                    <input type="email" id="perfil-email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Descripcion Breve de la Empresa</label>
                                    <textarea rows="4" id="perfil-descripcion" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none resize-none"></textarea>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-8">
                                <button type="button" class="px-6 py-2.5 text-gray-600 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg transition-colors">Cancelar</button>
                                <button type="submit" class="px-6 py-2.5 bg-uptex-green hover:bg-green-800 text-white font-medium rounded-lg shadow-md transition-colors flex items-center gap-2">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Contenedor de Toasts -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3"></div>

    <!-- Modal: CV del Alumno -->
    <div id="cv-modal" class="fixed inset-0 bg-gray-900/60 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-11/12 max-w-3xl overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]" id="cv-modal-content">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-uptex-lightgreen text-uptex-green flex items-center justify-center font-bold text-lg" id="modal-cv-initial">A</div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 leading-tight" id="modal-cv-name">Nombre Alumno</h3>
                        <p class="text-xs text-gray-500 font-medium" id="modal-cv-carrera">Carrera</p>
                    </div>
                </div>
                <button onclick="closeModal('cv-modal')" class="text-gray-400 hover:text-red-500 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-50">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="p-6 flex-1 overflow-y-auto bg-gray-50">
                <!-- Vista de documento CV -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden min-h-[440px] flex flex-col">
                    <iframe id="modal-cv-frame" src="" title="Vista previa del CV" class="w-full flex-1 min-h-[420px] bg-gray-100"></iframe>
                    <div id="modal-cv-sin-preview" class="hidden p-8 min-h-[420px] flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-file-alt text-6xl text-uptex-green mb-4"></i>
                        <p class="font-medium text-gray-600">Vista previa no disponible</p>
                        <p class="text-sm mt-2 text-center max-w-md">Abre o descarga el archivo para revisar el curriculum.</p>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap justify-end gap-3">
                    <a id="modal-cv-abrir" href="#" target="_blank" rel="noopener" class="px-4 py-2 bg-white hover:bg-gray-50 text-uptex-green rounded-lg text-sm font-medium transition-colors border border-green-200 flex items-center gap-2">
                        <i class="fas fa-up-right-from-square"></i> Abrir CV
                    </a>
                    <a id="modal-cv-link" href="#" download class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors border border-gray-300 flex items-center gap-2">
                        <i class="fas fa-download"></i> Descargar CV
                    </a>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-white flex justify-between items-center shrink-0">
                <a id="modal-correo-link" href="#" class="text-uptex-green hover:underline text-sm font-medium"><i class="fas fa-envelope mr-1"></i> Contactar por correo</a>
                <div id="modal-acciones-postulacion" class="flex gap-3 flex-wrap justify-end">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Nueva Vacante -->
    <div id="vacante-modal" class="fixed inset-0 bg-gray-900/60 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-11/12 max-w-2xl overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]" id="vacante-modal-content">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-uptex-green text-white">
                <h3 class="text-lg font-bold" id="vacante-modal-title">Publicar Nueva Vacante</h3>
                <button onclick="closeModal('vacante-modal')" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="p-6 flex-1 overflow-y-auto">
                <form id="form-nueva-vacante">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Titulo del Puesto</label>
                            <input type="text" id="vacante-puesto" required placeholder="Ej. Desarrollador Frontend Jr." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Modalidad</label>
                            <select id="vacante-jornada" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none bg-white">
                                <option>Presencial</option><option>Hibrido</option><option>Remoto (Home Office)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tipo de Contrato</label>
                            <select id="vacante-contrato" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none bg-white">
                                <option>Tiempo Completo</option><option>Medio Tiempo</option><option>Practicas/Estadias</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Sueldo Ofrecido</label>
                            <input type="number" id="vacante-sueldo" min="0" step="0.01" placeholder="Ej. 15000"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Perfil Requerido (Carrera UPTeX)</label>
                            <select id="vacante-categoria" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none bg-white">
                                <option>Ingenieria en Software</option>
                                <option>Ingenieria en Robotica</option>
                                <option>Licenciatura en Administracion y Gestion Empresarial</option>
                                <option>Cualquier carrera a fin</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Descripcion del Puesto</label>
                            <textarea id="vacante-descripcion" required rows="4" placeholder="Detalla las responsabilidades principales del puesto..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none resize-none"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Requerimientos</label>
                            <textarea id="vacante-requerimientos" rows="4" placeholder="Ej. Carrera requerida, experiencia, conocimientos tecnicos, idioma, disponibilidad..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none resize-none"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Aptitudes Asociadas</label>
                            <textarea id="vacante-aptitudes" rows="3" placeholder="Ej. Trabajo en equipo, liderazgo, comunicacion, resolucion de problemas..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none resize-none"></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 shrink-0">
                <button type="button" onclick="closeModal('vacante-modal')" class="px-5 py-2 text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg font-medium transition-colors text-sm">Cancelar</button>
                <button type="button" id="btn-guardar-vacante" onclick="guardarVacante()" class="px-5 py-2 bg-uptex-green hover:bg-green-800 text-white rounded-lg font-medium shadow-md transition-colors text-sm flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Publicar Ahora
                </button>
            </div>
        </div>
    </div>

    <script>
        // --- INICIALIZACION ---
        let charts = {};
        let vacantesEmpresa = [];
        let vacanteEditandoId = null;
        let postulacionSeleccionadaId = null;
        let datosDashboardEmpresa = null;
        const tokenAcceso = sessionStorage.getItem('token_acceso');
        let notificacionesEmpresa = [];
        let notificacionesEmpresaLeidas = sessionStorage.getItem('empresa_notificaciones_leidas') === '1';

        function escaparHtml(valor) {
            return String(valor ?? '').replace(/[&<>"']/g, (caracter) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[caracter]));
        }

        async function apiFetch(url, opciones = {}) {
            const respuesta = await fetch(url, {
                ...opciones,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${tokenAcceso}`,
                    ...(opciones.headers || {})
                }
            });

            if (respuesta.status === 401 || respuesta.status === 403) {
                sessionStorage.clear();
                localStorage.clear();
                window.location.replace('/');
                throw new Error('Sesion no autorizada');
            }

            const datos = await respuesta.json().catch(() => ({}));

            if (!respuesta.ok) {
                throw new Error(datos.mensaje || datos.message || 'No se pudo completar la operacion.');
            }

            return datos;
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('side-empresa-nombre').innerText = sessionStorage.getItem('usuario_nombre') || 'Empresa';
            document.getElementById('side-rep-nombre').innerText = sessionStorage.getItem('usuario_correo') || 'Representante';
            document.addEventListener('click', (event) => {
                document.querySelectorAll('[id^="menu-vacante-"]').forEach(menu => menu.classList.add('hidden'));
                const contenedorNotificaciones = document.getElementById('empresa-notificaciones');
                if (contenedorNotificaciones && !contenedorNotificaciones.contains(event.target)) {
                    const panel = document.getElementById('empresa-panel-notificaciones');
                    if (panel && !panel.classList.contains('hidden')) {
                        guardarPosicionPanelNotificacionesEmpresa(panel, 'empresa_panel_notificaciones_posicion');
                        panel.classList.add('hidden');
                    }
                }
            });
            cargarPerfilEmpresa();
            actualizarDashboard();
            cargarNotificacionesEmpresa();
            habilitarArrastrePanelNotificacionesEmpresa('empresa-panel-notificaciones', 'empresa-panel-notificaciones-header');
            setTimeout(() => {
                document.getElementById('dashboard-loading').style.display = 'none';
                document.getElementById('dashboard-content').style.opacity = '1';
            }, 5000);
        });

        // --- SISTEMA DE NAVEGACION Y UI ---
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const texts = document.querySelectorAll('.menu-text');
            const title = document.getElementById('sidebar-title');
            
            if (sidebar.classList.contains('w-64')) {
                sidebar.classList.replace('w-64', 'w-16');
                texts.forEach(t => t.classList.add('hidden'));
                title.classList.add('opacity-0');
            } else {
                sidebar.classList.replace('w-16', 'w-64');
                setTimeout(() => {
                    texts.forEach(t => t.classList.remove('hidden'));
                    title.classList.remove('opacity-0');
                }, 150);
            }
        }

        function cambiarPestana(tabId, btnElement) {
            document.querySelectorAll('.tab-content').forEach(p => {
                p.classList.add('hidden');
                p.classList.remove('fade-in');
            });
            
            document.querySelectorAll('.menu-btn').forEach(btn => {
                btn.className = "menu-btn w-full flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-uptex-green border-r-4 border-transparent transition-colors";
            });
            
            const targetTab = document.getElementById(tabId);
            targetTab.classList.remove('hidden');
            // Forzar reflow para reiniciar animacion
            void targetTab.offsetWidth;
            targetTab.classList.add('fade-in');

            if(btnElement) {
                btnElement.className = "menu-btn w-full flex items-center px-4 py-3 text-sm font-medium transition-colors bg-uptex-lightgreen text-uptex-green border-r-4 border-uptex-green";
            }

            // Cargar datos asincronos segun la pestana activa
            if(tabId === 'tab-dashboard') actualizarDashboard();
            if(tabId === 'tab-vacantes') cargarVacantesAPI();
            if(tabId === 'tab-solicitantes') cargarSolicitantesAPI();
            
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                if(sidebar.classList.contains('w-64')) toggleSidebar();
            }
        }

        // --- SISTEMA DE TOAST Y MODALES ---
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            let bgClass = type === 'success' ? 'bg-white border-l-4 border-green-500' :
                          type === 'error' ? 'bg-white border-l-4 border-red-500' :
                          'bg-white border-l-4 border-uptex-green';
            
            let icon = type === 'success' ? '<i class="fas fa-check-circle text-green-500"></i>' :
                       type === 'error' ? '<i class="fas fa-exclamation-circle text-red-500"></i>' :
                       '<i class="fas fa-info-circle text-uptex-green"></i>';

            toast.className = `flex items-center gap-3 px-4 py-3 shadow-lg rounded-lg min-w-[280px] toast-enter ${bgClass} text-gray-700`;
            toast.innerHTML = `
                <div class="text-xl">${icon}</div>
                <div class="font-medium text-sm flex-1">${message}</div>
                <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            `;
            container.appendChild(toast);
            
            setTimeout(() => {
                if(container.contains(toast)) {
                    toast.classList.replace('toast-enter', 'toast-leave');
                    setTimeout(() => toast.remove(), 300);
                }
            }, 3500);
        }

        async function cargarNotificacionesEmpresa() {
            try {
                const solicitantes = await apiFetch('/api/empresa/solicitantes');
                notificacionesEmpresa = solicitantes
                    .filter(solicitante => String(solicitante.estado || '').toLowerCase() === 'pendiente')
                    .slice(0, 5)
                    .map(solicitante => ({
                        titulo: solicitante.nombre || 'Nuevo postulante',
                        detalle: `${solicitante.vacante || 'Vacante'} - ${solicitante.carrera || 'Carrera no especificada'}`,
                        icono: 'fa-user-graduate'
                    }));

                const totalActual = String(notificacionesEmpresa.length);
                if (notificacionesEmpresa.length > 0 && sessionStorage.getItem('empresa_notificaciones_total') !== totalActual) {
                    notificacionesEmpresaLeidas = false;
                    sessionStorage.setItem('empresa_notificaciones_leidas', '0');
                    sessionStorage.setItem('empresa_notificaciones_total', totalActual);
                }

                renderNotificacionesEmpresa();
            } catch (error) {
                notificacionesEmpresa = [];
                renderNotificacionesEmpresa();
            }
        }

        function renderNotificacionesEmpresa() {
            const lista = document.getElementById('empresa-lista-notificaciones');
            const badge = document.getElementById('empresa-badge-notificaciones');
            if (!lista || !badge) return;

            if (!notificacionesEmpresa.length) {
                lista.innerHTML = '<div class="px-4 py-5 text-sm text-gray-500 text-center">No hay notificaciones nuevas.</div>';
                badge.classList.add('hidden');
                badge.classList.remove('flex');
                return;
            }

            if (notificacionesEmpresaLeidas) {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            } else {
                badge.textContent = notificacionesEmpresa.length > 9 ? '9+' : notificacionesEmpresa.length;
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            }

            lista.innerHTML = notificacionesEmpresa.map(item => `
                <div class="px-4 py-3 border-b border-gray-100 last:border-b-0 flex gap-3 items-start hover:bg-uptex-lightgreen/40">
                    <span class="w-8 h-8 rounded-full bg-uptex-lightgreen text-uptex-green flex items-center justify-center shrink-0">
                        <i class="fas ${escaparHtml(item.icono)}"></i>
                    </span>
                    <div class="min-w-0 text-left">
                        <strong class="block text-sm text-gray-800">${escaparHtml(item.titulo)}</strong>
                        <span class="block text-xs text-gray-500 mt-0.5 leading-snug break-words">${escaparHtml(item.detalle)}</span>
                    </div>
                </div>
            `).join('');
        }

        function toggleNotificacionesEmpresa(event) {
            event.stopPropagation();
            const panel = document.getElementById('empresa-panel-notificaciones');
            const boton = event.currentTarget;
            const seAbrira = panel?.classList.contains('hidden');

            if (seAbrira) {
                panel?.classList.remove('hidden');
                requestAnimationFrame(() => {
                    restaurarOPosicionarPanelNotificacionesEmpresa(panel, boton, 'empresa_panel_notificaciones_posicion');
                });
            } else {
                guardarPosicionPanelNotificacionesEmpresa(panel, 'empresa_panel_notificaciones_posicion');
                panel?.classList.add('hidden');
            }
        }

        function restaurarOPosicionarPanelNotificacionesEmpresa(panel, boton, storageKey) {
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
                limitarPanelNotificacionesEmpresaAlViewport(panel);
                return;
            }

            posicionarPanelNotificacionesEmpresa(panel, boton);
        }

        function posicionarPanelNotificacionesEmpresa(panel, boton) {
            if (!panel || !boton) return;

            const rect = boton.getBoundingClientRect();
            const ancho = Math.min(352, window.innerWidth - 24);
            const top = Math.max(12, Math.min(rect.bottom + 10, window.innerHeight - 160));
            const altoMaximo = Math.max(180, window.innerHeight - top - 12);

            panel.style.width = `${ancho}px`;
            panel.style.top = `${top}px`;
            panel.style.left = `${Math.max(12, Math.min(window.innerWidth - ancho - 12, rect.right - ancho))}px`;
            panel.style.right = 'auto';
            panel.style.maxHeight = `${altoMaximo}px`;
        }

        window.addEventListener('resize', () => {
            const panel = document.getElementById('empresa-panel-notificaciones');
            if (panel && !panel.classList.contains('hidden')) limitarPanelNotificacionesEmpresaAlViewport(panel);
        });

        function limitarPanelNotificacionesEmpresaAlViewport(panel) {
            const rect = panel.getBoundingClientRect();
            const ancho = panel.offsetWidth || rect.width;
            const alto = panel.offsetHeight || rect.height;
            const nuevoX = Math.max(12, Math.min(window.innerWidth - ancho - 12, rect.left));
            const nuevoY = Math.max(12, Math.min(window.innerHeight - alto - 12, rect.top));

            panel.style.left = `${nuevoX}px`;
            panel.style.top = `${nuevoY}px`;
            panel.style.right = 'auto';
        }

        function habilitarArrastrePanelNotificacionesEmpresa(panelId, headerId) {
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
                    guardarPosicionPanelNotificacionesEmpresa(panel, 'empresa_panel_notificaciones_posicion');
                }
            });

            header.addEventListener('pointercancel', (event) => {
                if (header.hasPointerCapture(event.pointerId)) {
                    header.releasePointerCapture(event.pointerId);
                    guardarPosicionPanelNotificacionesEmpresa(panel, 'empresa_panel_notificaciones_posicion');
                }
            });
        }

        function guardarPosicionPanelNotificacionesEmpresa(panel, storageKey) {
            const rect = panel.getBoundingClientRect();
            localStorage.setItem(storageKey, JSON.stringify({
                left: rect.left,
                top: rect.top,
                width: rect.width,
                height: rect.height
            }));
        }

        function marcarNotificacionesEmpresaLeidas() {
            notificacionesEmpresaLeidas = true;
            sessionStorage.setItem('empresa_notificaciones_leidas', '1');
            const badge = document.getElementById('empresa-badge-notificaciones');
            badge?.classList.add('hidden');
            badge?.classList.remove('flex');
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            const content = document.getElementById(`${modalId}-content`);
            modal.classList.remove('hidden');
            setTimeout(() => content.classList.replace('scale-95', 'scale-100'), 10);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            const content = document.getElementById(`${modalId}-content`);
            content.classList.replace('scale-100', 'scale-95');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        async function cerrarSesion() {
            showToast('Cerrando sesion de forma segura...', 'info');
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

        // --- Datos reales de empresa ---
        async function cargarPerfilEmpresa() {
            try {
                const data = await apiFetch('/api/empresa/perfil');
                const empresa = data.empresa || {};
                const representante = data.representante || {};

                document.getElementById('side-empresa-nombre').innerText = empresa.nombre || representante.nombre || 'Empresa';
                document.getElementById('side-rep-nombre').innerText = representante.correo || 'Representante';
                document.getElementById('perfil-nombre').value = empresa.nombre || '';
                document.getElementById('perfil-sector').value = empresa.sector || document.getElementById('perfil-sector').value;
                document.getElementById('perfil-web').value = empresa.web || '';
                document.getElementById('perfil-representante').value = representante.nombre || '';
                document.getElementById('perfil-email').value = empresa.email || representante.correo || '';
                document.getElementById('perfil-descripcion').value = empresa.descripcion || '';
            } catch (error) {
                showToast(error.message || 'No se pudo cargar el perfil de la empresa.', 'error');
            }
        }

        async function actualizarDashboard() {
            const loader = document.getElementById('dashboard-loading');
            const content = document.getElementById('dashboard-content');
            loader.style.display = 'flex';
            content.classList.add('fade-in');
            content.style.opacity = '1';

            try {
                const data = await apiFetch('/api/empresa/dashboard');
                datosDashboardEmpresa = data;

                animarContador('kpi-vacantes', data.metricas?.vacantes || 0);
                animarContador('kpi-postulantes', data.metricas?.postulantes || 0);
                animarContador('kpi-tasa', data.metricas?.tasa || 0, '%');
                animarContador('kpi-visitas', data.metricas?.visitas || 0);

                document.getElementById('funnel-1').innerText = data.funnel?.pendientes || 0;
                document.getElementById('funnel-2').innerText = data.funnel?.entrevista || 0;
                document.getElementById('funnel-3').innerText = data.funnel?.rechazados || 0;
                document.getElementById('funnel-4').innerText = data.funnel?.contratados || 0;

                dibujarGraficasEmpresa(data.graficas || {}, data.funnel || {});
                loader.style.display = 'none';
                content.classList.add('fade-in');
                content.style.opacity = '1';
            } catch (error) {
                showToast(error.message || 'Error de conexion al servidor.', 'error');
                loader.style.display = 'none';
                content.style.opacity = '1';
            }
        }

        function animarContador(id, finalValue, suffix = '') {
            const el = document.getElementById(id);
            const objetivo = Number(finalValue) || 0;
            let current = 0;
            const increment = Math.max(1, Math.ceil(objetivo / 20));
            const timer = setInterval(() => {
                current += increment;
                if(current >= objetivo) {
                    current = objetivo;
                    clearInterval(timer);
                }
                el.innerText = current + suffix;
            }, 30);
        }

        function dibujarGraficasEmpresa(dataGraficas, dataFunnel) {
            const perfilesLabels = dataGraficas.perfiles?.labels?.length ? dataGraficas.perfiles.labels : ['Sin datos'];
            const perfilesData = dataGraficas.perfiles?.data?.length ? dataGraficas.perfiles.data : [0];
            const evolucionLabels = dataGraficas.evolucion?.labels?.length ? dataGraficas.evolucion.labels : ['Sin datos'];
            const evolucionData = dataGraficas.evolucion?.data?.length ? dataGraficas.evolucion.data : [0];

            crearChart('graficaPerfiles', 'bar', {
                labels: perfilesLabels,
                datasets: [{
                    label: 'Vacantes',
                    data: perfilesData,
                    backgroundColor: ['#006837', '#5bc0de', '#C1272D', '#6c757d', '#28a745'],
                    borderRadius: 12,
                    barPercentage: 0.62,
                    categoryPercentage: 0.72
                }]
            }, { indexAxis: 'y' });

            crearChart('graficaEvolucion', 'line', {
                labels: evolucionLabels,
                datasets: [{
                    label: 'Vacantes publicadas',
                    data: evolucionData,
                    borderColor: '#006837',
                    backgroundColor: 'rgba(0, 104, 55, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#006837',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7
                }]
            });

            crearChart('graficaEmbudo', 'doughnut', {
                labels: ['Pendientes', 'En Entrevista', 'Rechazados', 'Contratados'],
                datasets: [{
                    data: [
                        dataFunnel.pendientes || 0,
                        dataFunnel.entrevista || 0,
                        dataFunnel.rechazados || 0,
                        dataFunnel.contratados || 0
                    ],
                    backgroundColor: ['#9ca3af', '#006837', '#C1272D', '#28a745'],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 8
                }]
            }, { cutout: '65%' });
        }

        function crearChart(canvasId, type, data, extraOptions = {}) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            if (charts[canvasId]) charts[canvasId].destroy();
            Chart.defaults.font.family = 'Inter, Segoe UI, sans-serif';
            Chart.defaults.color = '#68746f';

            const baseOptions = {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 900, easing: 'easeOutQuart' },
                plugins: {
                    legend: {
                        display: type === 'doughnut',
                        position: 'right',
                        labels: { usePointStyle: true, boxWidth: 8, padding: 16 }
                    },
                    tooltip: {
                        backgroundColor: '#24342d',
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false
                    }
                },
                scales: type !== 'doughnut' ? {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(104,116,111,0.14)' }, border: { display: false } },
                    x: { grid: { display: false }, border: { display: false } }
                } : {}
            };

            const options = { ...baseOptions, ...extraOptions };
            if(extraOptions.indexAxis === 'y') {
                options.scales.y.grid = { display: false };
                options.scales.x.grid = { color: '#f3f4f6' };
            }

            charts[canvasId] = new Chart(ctx, { type, data, options });
        }

        async function exportarPDFEmpresa() {
            if (!datosDashboardEmpresa) {
                showToast('Espera a que carguen los datos del dashboard.', 'info');
                return;
            }

            const boton = document.getElementById('btnExportarPDFEmpresa');
            const textoOriginal = boton.innerHTML;
            boton.disabled = true;
            boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando PDF';
            const loader = document.getElementById('dashboard-loading');
            const estadoLoader = loader.style.display;

            try {
                if (!window.html2canvas || !window.jspdf?.jsPDF) {
                    throw new Error('No se cargaron las librerias para generar PDF.');
                }

                const contenido = document.querySelector('#tab-dashboard');
                loader.style.display = 'none';

                const canvas = await html2canvas(contenido, {
                    scale: 2,
                    backgroundColor: '#f9fafb',
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

                pdf.save(`dashboard-empresa-${fechaArchivoEmpresa()}.pdf`);
                showToast('PDF descargado correctamente.', 'success');
            } catch (error) {
                console.error('Error al exportar PDF:', error);
                showToast('No se pudo generar el PDF. Verifica tu conexion e intenta de nuevo.', 'error');
            } finally {
                loader.style.display = estadoLoader === 'flex' ? 'flex' : 'none';
                boton.disabled = false;
                boton.innerHTML = textoOriginal;
            }
        }

        function exportarExcelEmpresa() {
            if (!datosDashboardEmpresa) {
                showToast('Espera a que carguen los datos del dashboard.', 'info');
                return;
            }

            const boton = document.getElementById('btnExportarExcelEmpresa');
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
                    <h1>Dashboard Empresa UPTeX</h1>
                    <p>Generado: ${escaparHtml(new Date().toLocaleString('es-MX'))}</p>
                    ${tablaExcelEmpresa('Metricas', [
                        ['Metrica', 'Valor'],
                        ['Vacantes activas', datosDashboardEmpresa.metricas?.vacantes ?? 0],
                        ['Total postulantes', datosDashboardEmpresa.metricas?.postulantes ?? 0],
                        ['Tasa de contratacion', `${datosDashboardEmpresa.metricas?.tasa ?? 0}%`],
                        ['Visitas al perfil', datosDashboardEmpresa.metricas?.visitas ?? 0]
                    ])}
                    ${tablaExcelEmpresa('Embudo de postulantes', [
                        ['Estado', 'Total'],
                        ['Pendientes', datosDashboardEmpresa.funnel?.pendientes ?? 0],
                        ['En entrevista', datosDashboardEmpresa.funnel?.entrevista ?? 0],
                        ['Rechazados', datosDashboardEmpresa.funnel?.rechazados ?? 0],
                        ['Contratados', datosDashboardEmpresa.funnel?.contratados ?? 0]
                    ])}
                    ${tablaExcelEmpresa('Perfiles demandados', [['Perfil', 'Vacantes'], ...filasGraficaEmpresa(datosDashboardEmpresa.graficas?.perfiles)])}
                    ${tablaExcelEmpresa('Actividad de postulaciones', [['Periodo', 'Total'], ...filasGraficaEmpresa(datosDashboardEmpresa.graficas?.evolucion)])}
                </body>
                </html>
            `;

            const blob = new Blob(['\ufeff' + htmlExcel], { type: 'application/vnd.ms-excel;charset=utf-8;' });
            const enlace = document.createElement('a');
            enlace.href = URL.createObjectURL(blob);
            enlace.download = `dashboard-empresa-${fechaArchivoEmpresa()}.xls`;
            document.body.appendChild(enlace);
            enlace.click();
            document.body.removeChild(enlace);
            URL.revokeObjectURL(enlace.href);
            boton.disabled = false;
            boton.innerHTML = textoOriginal;
            showToast('Excel descargado correctamente.', 'success');
        }

        function filasGraficaEmpresa(grafica) {
            const etiquetas = Array.isArray(grafica?.labels) ? grafica.labels : ['Sin datos'];
            const valores = Array.isArray(grafica?.data) ? grafica.data : [0];
            return etiquetas.map((etiqueta, index) => [etiqueta, valores[index] ?? 0]);
        }

        function tablaExcelEmpresa(titulo, filas) {
            return `
                <h2>${escaparHtml(titulo)}</h2>
                <table>
                    ${filas.map((fila, index) => `
                        <tr>
                            ${fila.map(celda => index === 0 ? `<th>${escaparHtml(celda)}</th>` : `<td>${escaparHtml(celda)}</td>`).join('')}
                        </tr>
                    `).join('')}
                </table>
            `;
        }

        function fechaArchivoEmpresa() {
            return new Date().toISOString().slice(0, 10);
        }

        async function cargarVacantesAPI() {
            const contenedor = document.getElementById('lista-vacantes');
            contenedor.innerHTML = '<div class="col-span-full flex justify-center py-10"><i class="fas fa-spinner fa-spin text-3xl text-uptex-green"></i></div>';

            try {
                const vacantes = await apiFetch('/api/empresa/vacantes');
                vacantesEmpresa = vacantes;

                if (!vacantes.length) {
                    contenedor.innerHTML = '<div class="col-span-full bg-white border border-gray-100 rounded-xl p-8 text-center text-gray-500">No hay vacantes publicadas por tu empresa.</div>';
                    return;
                }

                let html = '';
                vacantes.forEach(v => {
                    const badgeColor = v.estado === 'Activa' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-gray-100 text-gray-600 border-gray-200';
                    html += `
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow relative group">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-md border ${badgeColor}">${escaparHtml(v.estado)}</span>
                        <div class="relative">
                            <button onclick="toggleMenuVacante(event, ${v.id})" class="text-gray-400 hover:text-uptex-green w-8 h-8 rounded-full hover:bg-gray-50" title="Opciones">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div id="menu-vacante-${v.id}" class="hidden absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-20 overflow-hidden">
                                <button onclick="abrirEditarVacante(${v.id})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-uptex-lightgreen hover:text-uptex-green flex items-center gap-2">
                                    <i class="fas fa-pen"></i> Editar vacante
                                </button>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">${escaparHtml(v.titulo)}</h3>
                    <p class="text-sm text-gray-500 mb-4 flex items-center gap-2"><i class="fas fa-graduation-cap"></i> ${escaparHtml(v.perfil)}</p>
                    <div class="flex gap-4 mb-5 border-t border-gray-100 pt-4">
                        <div class="flex-1 text-center">
                            <p class="text-2xl font-bold text-gray-800">${Number(v.postulantes) || 0}</p>
                            <p class="text-xs text-gray-500 uppercase font-semibold">Candidatos</p>
                        </div>
                        <div class="w-px bg-gray-100"></div>
                        <div class="flex-1 text-center flex flex-col justify-center">
                            <p class="text-sm font-medium text-gray-700"><i class="fas fa-map-marker-alt text-gray-400"></i> ${escaparHtml(v.tipo || 'Sin modalidad')}</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-400">Publicado ${escaparHtml(v.fecha || '')}</span>
                        <button onclick="cambiarPestana('tab-solicitantes')" class="text-uptex-green hover:underline font-medium">Ver proceso &rarr;</button>
                    </div>
                </div>`;
                });
                contenedor.innerHTML = html;
            } catch (error) {
                contenedor.innerHTML = '<div class="col-span-full bg-white border border-red-100 rounded-xl p-8 text-center text-red-600">No se pudieron cargar las vacantes.</div>';
                showToast(error.message, 'error');
            }
        }

        async function cargarSolicitantesAPI() {
            const tbody = document.getElementById('tabla-solicitantes-body');
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-10"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i></td></tr>';

            try {
                const solicitantes = await apiFetch('/api/empresa/solicitantes');

                if (!solicitantes.length) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-10 text-gray-500">No hay postulaciones registradas para tus vacantes.</td></tr>';
                    return;
                }

                let html = '';
                solicitantes.forEach(s => {
                    let badge = '';
                    if(s.estado === 'Pendiente') badge = 'bg-gray-100 text-gray-700';
                    else if(s.estado === 'En Entrevista') badge = 'bg-uptex-lightgreen text-uptex-green';
                    else if(s.estado === 'Contratado') badge = 'bg-green-100 text-green-700';
                    else badge = 'bg-red-100 text-red-700';

                    html += `
                <tr class="hover:bg-uptex-lightgreen/30 transition-colors group">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-uptex-lightgreen text-uptex-green flex items-center justify-center font-bold text-xs">${escaparHtml(s.nombre).charAt(0)}</div>
                            <div>
                                <p class="font-bold text-gray-800 text-sm">${escaparHtml(s.nombre)}</p>
                                <p class="text-xs text-gray-400">ID: ${escaparHtml(s.id)}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-gray-600 text-sm">${escaparHtml(s.carrera)}</td>
                    <td class="py-4 px-6 text-gray-800 text-sm font-medium">${escaparHtml(s.vacante)}</td>
                    <td class="py-4 px-6 text-gray-500 text-sm">${escaparHtml(s.fecha)}</td>
                    <td class="py-4 px-6"><span class="px-2.5 py-1 text-xs font-semibold rounded-full ${badge}">${escaparHtml(s.estado)}</span></td>
                    <td class="py-4 px-6 text-center">
                        <button onclick="abrirCvDesdeBoton(this)" data-id="${escaparHtml(s.id)}" data-nombre="${escaparHtml(s.nombre)}" data-correo="${escaparHtml(s.correo || '')}" data-carrera="${escaparHtml(s.carrera)}" data-vacante="${escaparHtml(s.vacante || '')}" data-estado="${escaparHtml(s.estado || '')}" data-cv-url="${escaparHtml(s.cv_url || '')}" class="px-3 py-1.5 bg-white border border-gray-300 hover:border-uptex-green text-gray-600 hover:text-uptex-green rounded text-sm font-medium transition-colors shadow-sm">
                            <i class="fas fa-file-alt mr-1"></i> Ver CV
                        </button>
                    </td>
                </tr>`;
                });
                tbody.innerHTML = html;
            } catch (error) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-10 text-red-600">No se pudieron cargar los solicitantes.</td></tr>';
                showToast(error.message, 'error');
            }
        }
        // Interacciones Especificas
        function abrirCvDesdeBoton(boton) {
            verPerfilCandidato({
                id: boton.dataset.id,
                nombre: boton.dataset.nombre,
                correo: boton.dataset.correo,
                carrera: boton.dataset.carrera,
                vacante: boton.dataset.vacante,
                estado: boton.dataset.estado,
                cvUrl: boton.dataset.cvUrl
            });
        }

        function verPerfilCandidato(solicitante) {
            const cvUrl = solicitante.cvUrl || '#';
            postulacionSeleccionadaId = solicitante.id;
            document.getElementById('modal-cv-name').innerText = solicitante.nombre || 'Solicitante';
            document.getElementById('modal-cv-carrera').innerText = solicitante.carrera || 'Carrera no especificada';
            document.getElementById('modal-cv-initial').innerText = (solicitante.nombre || 'S').charAt(0);
            document.getElementById('modal-cv-link').href = cvUrl;
            document.getElementById('modal-cv-abrir').href = cvUrl;
            document.getElementById('modal-cv-link').setAttribute('download', `CV-${normalizarNombreArchivo(solicitante.nombre || 'solicitante')}`);
            document.getElementById('modal-correo-link').href = crearMailto(solicitante);
            renderAccionesPostulacion(solicitante.estado || 'Pendiente');

            const frame = document.getElementById('modal-cv-frame');
            const sinPreview = document.getElementById('modal-cv-sin-preview');
            const puedePrevisualizar = cvUrl !== '#' && cvUrl.toLowerCase().split('?')[0].endsWith('.pdf');

            if (puedePrevisualizar) {
                frame.src = cvUrl;
                frame.classList.remove('hidden');
                sinPreview.classList.add('hidden');
            } else {
                frame.src = '';
                frame.classList.add('hidden');
                sinPreview.classList.remove('hidden');
            }

            openModal('cv-modal');
        }

        function renderAccionesPostulacion(estado) {
            const contenedor = document.getElementById('modal-acciones-postulacion');
            const estadoActual = String(estado || '').toLowerCase();

            if (estadoActual === 'contratado' || estadoActual === 'rechazado') {
                contenedor.innerHTML = '<span class="px-3 py-2 text-xs font-semibold rounded-lg bg-gray-100 text-gray-500">Proceso finalizado</span>';
                return;
            }

            const botonDescartar = `<button type="button" onclick="cambiarEstadoPostulacion('Rechazado', this)" class="px-4 py-2 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg font-medium transition-colors text-sm">Descartar</button>`;
            const botonContratar = `<button type="button" onclick="cambiarEstadoPostulacion('Contratado', this)" class="px-4 py-2 bg-uptex-green hover:bg-green-700 text-white rounded-lg font-medium shadow-sm transition-colors text-sm flex items-center gap-2"><i class="fas fa-check"></i> Contratar</button>`;

            if (estadoActual === 'en entrevista') {
                contenedor.innerHTML = botonDescartar + botonContratar;
                return;
            }

            contenedor.innerHTML = botonDescartar
                + `<button type="button" onclick="cambiarEstadoPostulacion('En Entrevista', this)" class="px-4 py-2 text-uptex-green bg-uptex-lightgreen hover:bg-uptex-lightgreen border border-green-200 rounded-lg font-medium transition-colors text-sm">Mover a Entrevista</button>`
                + botonContratar;
        }

        function crearMailto(solicitante) {
            const correo = solicitante.correo || '';
            const asunto = encodeURIComponent(`Seguimiento a tu postulacion - ${solicitante.vacante || 'UPTeX'}`);
            const cuerpo = encodeURIComponent(`Hola ${solicitante.nombre || ''},\n\nTe contactamos por tu postulacion a ${solicitante.vacante || 'nuestra vacante'}.\n\nSaludos.`);
            return correo ? `mailto:${correo}?subject=${asunto}&body=${cuerpo}` : '#';
        }

        function normalizarNombreArchivo(nombre) {
            return String(nombre)
                .trim()
                .replace(/\s+/g, '-')
                .replace(/[^a-zA-Z0-9-_]/g, '')
                .toLowerCase() || 'solicitante';
        }

        async function cambiarEstadoPostulacion(nuevoEstado, boton = null) {
            if (!postulacionSeleccionadaId) {
                showToast('No se encontro la postulacion seleccionada.', 'error');
                return;
            }

            const textoOriginal = boton?.innerHTML;
            if (boton) {
                boton.disabled = true;
                boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando';
            }

            try {
                await apiFetch(`/api/empresa/postulaciones/${postulacionSeleccionadaId}/estado`, {
                    method: 'PUT',
                    body: JSON.stringify({ estado: nuevoEstado })
                });

                showToast(`Estatus actualizado a: ${nuevoEstado}`, 'success');
                closeModal('cv-modal');
                postulacionSeleccionadaId = null;
                await cargarSolicitantesAPI();
                await actualizarDashboard();
                await cargarNotificacionesEmpresa();
            } catch (error) {
                showToast(error.message || 'No se pudo actualizar el estatus.', 'error');
            } finally {
                if (boton) {
                    boton.disabled = false;
                    boton.innerHTML = textoOriginal;
                }
            }
        }
        function openModalNuevaVacante() {
            document.getElementById('form-nueva-vacante').reset();
            vacanteEditandoId = null;
            document.getElementById('vacante-modal-title').innerText = 'Publicar Nueva Vacante';
            document.getElementById('btn-guardar-vacante').innerHTML = '<i class="fas fa-paper-plane"></i> Publicar Ahora';
            openModal('vacante-modal');
        }

        function toggleMenuVacante(event, id) {
            event.stopPropagation();
            document.querySelectorAll('[id^="menu-vacante-"]').forEach(menu => {
                if (menu.id !== `menu-vacante-${id}`) {
                    menu.classList.add('hidden');
                }
            });
            document.getElementById(`menu-vacante-${id}`)?.classList.toggle('hidden');
        }

        function abrirEditarVacante(id) {
            const vacante = vacantesEmpresa.find(item => Number(item.id) === Number(id));
            if (!vacante) {
                showToast('No se encontro la vacante seleccionada.', 'error');
                return;
            }

            vacanteEditandoId = id;
            document.getElementById('form-nueva-vacante').reset();
            document.getElementById('vacante-modal-title').innerText = 'Editar Vacante';
            document.getElementById('btn-guardar-vacante').innerHTML = '<i class="fas fa-save"></i> Guardar Cambios';
            document.getElementById('vacante-puesto').value = vacante.puesto || vacante.titulo || '';
            document.getElementById('vacante-jornada').value = vacante.tipo_jornada || vacante.tipo || 'Presencial';
            document.getElementById('vacante-contrato').value = vacante.tipo_contrato || 'Tiempo Completo';
            document.getElementById('vacante-sueldo').value = vacante.salario ?? '';
            document.getElementById('vacante-categoria').value = vacante.categoria || vacante.perfil || 'Cualquier carrera a fin';
            document.getElementById('vacante-descripcion').value = vacante.descripcion || '';
            document.getElementById('vacante-requerimientos').value = vacante.requerimientos || '';
            document.getElementById('vacante-aptitudes').value = vacante.aptitudes || '';
            document.getElementById(`menu-vacante-${id}`)?.classList.add('hidden');
            openModal('vacante-modal');
        }

        async function guardarVacante() {
            const form = document.getElementById('form-nueva-vacante');
            if(!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            try {
                const url = vacanteEditandoId ? `/api/empresa/vacantes/${vacanteEditandoId}` : '/api/empresa/vacantes';
                const method = vacanteEditandoId ? 'PUT' : 'POST';

                await apiFetch(url, {
                    method,
                    body: JSON.stringify({
                        puesto: document.getElementById('vacante-puesto').value,
                        tipo_jornada: document.getElementById('vacante-jornada').value,
                        tipo_contrato: document.getElementById('vacante-contrato').value,
                        categoria: document.getElementById('vacante-categoria').value,
                        salario_ofrecido: document.getElementById('vacante-sueldo').value || null,
                        descripcion: document.getElementById('vacante-descripcion').value,
                        requerimientos: document.getElementById('vacante-requerimientos').value,
                        aptitudes: document.getElementById('vacante-aptitudes').value
                    })
                });

                showToast(vacanteEditandoId ? 'Vacante actualizada correctamente.' : 'Vacante publicada exitosamente.', 'success');
                closeModal('vacante-modal');
                form.reset();
                vacanteEditandoId = null;
                cargarVacantesAPI();
                actualizarDashboard();
            } catch (error) {
                showToast(error.message, 'error');
            }
        }
        
        document.getElementById('form-perfil-empresa').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = e.target.querySelector('button[type="submit"]');
            const textoOriginal = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            btn.disabled = true;
            
            try {
                await apiFetch('/api/empresa/perfil', {
                    method: 'PUT',
                    body: JSON.stringify({
                        sector: document.getElementById('perfil-sector').value,
                        web: document.getElementById('perfil-web').value,
                        email: document.getElementById('perfil-email').value,
                        descripcion: document.getElementById('perfil-descripcion').value
                    })
                });

                showToast('Perfil de empresa actualizado.', 'success');
                cargarPerfilEmpresa();
            } catch (error) {
                showToast(error.message, 'error');
            } finally {
                btn.innerHTML = textoOriginal;
                btn.disabled = false;
            }
        });

    </script>
</body>
</html>









