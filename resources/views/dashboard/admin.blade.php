<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPTeX - Panel de Administracion</title>
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
        
        /* Transiciones suaves para la sidebar */
        .sidebar-transition { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        .mobile-menu-trigger { display: none; }
        .chart-mobile-frame {
            position: relative;
            width: 100%;
            min-height: 16rem;
        }
        .chart-mobile-frame canvas {
            display: block;
            width: 100% !important;
            height: 100% !important;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Estilos para el Toast (Notificaciones) */
        .toast-enter { animation: toastEnter 0.3s forwards; }
        .toast-leave { animation: toastLeave 0.3s forwards; }
        
        @keyframes toastEnter {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes toastLeave {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        @media (max-width: 768px) {
            body {
                display: block;
                min-height: 100vh;
                overflow: hidden;
            }

            #sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                height: 100vh;
                width: min(82vw, 16rem) !important;
                transform: translateX(-100%);
                box-shadow: 0 20px 45px rgba(0, 0, 0, 0.18);
            }

            #sidebar.sidebar-mobile-open {
                transform: translateX(0);
            }

            #sidebar .menu-text,
            #sidebar-title {
                display: inline !important;
                opacity: 1 !important;
            }

            main {
                width: 100%;
                height: 100vh;
            }

            header {
                min-height: 4rem;
            }

            .mobile-menu-trigger {
                display: flex;
            }

            .tab-content {
                min-width: 0;
            }

            .chart-mobile-frame {
                height: 18rem !important;
                min-height: 18rem;
            }
        }
    </style>

    <!-- SCRIPT DE SEGURIDAD -->
    <script>
        // Validacion de sesion de administrador
        function validarSesionAdmin() {
            const tokenActual = sessionStorage.getItem('token_acceso');
            const rolActual = sessionStorage.getItem('usuario_rol');
            if (!tokenActual || !rolActual || rolActual.toLowerCase() !== 'admin') {
                // En produccion: window.location.replace('/');
                window.location.replace('/');
            }
        }
        validarSesionAdmin();
        window.addEventListener('pageshow', validarSesionAdmin);
    </script>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden text-gray-800">

    <aside id="sidebar" class="sidebar-transition w-64 bg-white border-r border-gray-200 flex flex-col z-40 relative group">
        <div class="h-16 bg-uptex-green text-white flex items-center justify-between px-4">
            <h2 id="sidebar-title" class="font-bold text-lg whitespace-nowrap overflow-hidden transition-opacity duration-300">Panel Admin</h2>
            <button onclick="toggleSidebar()" class="text-white hover:bg-white/20 p-2 rounded-md transition-colors">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <ul class="flex-1 py-4 space-y-1 overflow-y-auto no-scrollbar">
            <!-- Items del menu -->
            <li>
                <button onclick="cambiarPestana('tab-dashboard', this)" class="menu-btn w-full flex items-center px-4 py-3 text-sm font-medium transition-colors bg-uptex-lightgreen text-uptex-green border-r-4 border-uptex-green">
                    <i class="fas fa-chart-pie w-6 text-center text-lg"></i>
                    <span class="menu-text ml-3 whitespace-nowrap">Dashboard Analitico</span>
                </button>
            </li>
            <li>
                <button onclick="cambiarPestana('tab-alta', this)" class="menu-btn w-full flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-uptex-green border-r-4 border-transparent transition-colors">
                    <i class="fas fa-user-plus w-6 text-center text-lg"></i>
                    <span class="menu-text ml-3 whitespace-nowrap">Alta de Empresas</span>
                </button>
            </li>
            <li>
                <button onclick="cambiarPestana('tab-empresas', this)" class="menu-btn w-full flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-uptex-green border-r-4 border-transparent transition-colors">
                    <i class="fas fa-building w-6 text-center text-lg"></i>
                    <span class="menu-text ml-3 whitespace-nowrap">Control Empresas</span>
                </button>
            </li>
            <li>
                <button onclick="cambiarPestana('tab-egresados', this)" class="menu-btn w-full flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-uptex-green border-r-4 border-transparent transition-colors">
                    <i class="fas fa-user-graduate w-6 text-center text-lg"></i>
                    <span class="menu-text ml-3 whitespace-nowrap">Control Egresados</span>
                </button>
            </li>
            <li>
                <button onclick="cambiarPestana('tab-usuarios-aceptados', this)" class="menu-btn w-full flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-uptex-green border-r-4 border-transparent transition-colors">
                    <i class="fas fa-user-check w-6 text-center text-lg"></i>
                    <span class="menu-text ml-3 whitespace-nowrap">Usuarios Aceptados</span>
                </button>
            </li>
        </ul>
        
        <!-- Boton de logout en sidebar (para moviles) -->
        <div class="p-4 border-t border-gray-200">
            <button onclick="cerrarSesion()" class="w-full flex items-center px-2 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-md transition-colors">
                <i class="fas fa-sign-out-alt w-6 text-center text-lg"></i>
                <span class="menu-text ml-3 whitespace-nowrap">Cerrar Sesion</span>
            </button>
        </div>
    </aside>

    <main class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 shadow-sm z-30">
            <div class="flex items-center gap-3 min-w-0">
                <button onclick="toggleSidebar()" class="mobile-menu-trigger text-uptex-green hover:bg-uptex-lightgreen w-10 h-10 rounded-lg transition-colors items-center justify-center shrink-0" title="Abrir menu">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="text-xl font-bold text-uptex-green hidden sm:block">Bolsa de Trabajo Inteligente - UPTeX</h1>
                <h1 class="text-lg font-bold text-uptex-green sm:hidden">UPTeX</h1>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-uptex-green text-white flex items-center justify-center font-bold">
                        <i class="fas fa-user"></i>
                    </div>
                    <span id="nombre-admin" class="text-sm font-medium text-gray-700 hidden md:block">Cargando...</span>
                </div>
            </div>
        </header>

        <!-- Contenedor Principal (Scrollable) -->
        <div class="flex-1 overflow-auto p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                
                <!-- PESTANA 1: DASHBOARD -->
                <div id="tab-dashboard" class="tab-content block fade-in">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 pb-4 border-b border-gray-200">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Dashboard Analitico</h2>
                            <p class="text-sm text-gray-500 mt-1">Indicadores principales de empleabilidad y actividad empresarial.</p>
                        </div>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <button id="btnExportarPDFAdmin" onclick="exportarPDFAdmin()" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-uptex-red hover:bg-red-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                <i class="fas fa-file-pdf"></i> Exportar PDF
                            </button>
                            <button id="btnExportarExcelAdmin" onclick="exportarExcelAdmin()" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>

                    <!-- KPIs -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- KPI 1 -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group">
                            <div class="absolute right-0 bottom-0 opacity-10 group-hover:opacity-20 transition-opacity transform group-hover:scale-110 duration-300">
                                <i class="fas fa-chart-pie text-8xl text-uptex-green -mr-4 -mb-4"></i>
                            </div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Empleabilidad</h3>
                            <div class="text-3xl font-extrabold text-gray-800 mb-1" id="kpi-empleabilidad">--%</div>
                            <p class="text-xs text-gray-500 font-medium">Egresados contratados</p>
                        </div>
                        <!-- KPI 2 -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group">
                            <div class="absolute right-0 bottom-0 opacity-10 group-hover:opacity-20 transition-opacity transform group-hover:scale-110 duration-300">
                                <i class="fas fa-money-bill-wave text-8xl text-yellow-500 -mr-4 -mb-4"></i>
                            </div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Salario Promedio</h3>
                            <div class="text-3xl font-extrabold text-gray-800 mb-1" id="kpi-salario">$ --</div>
                            <p class="text-xs text-gray-500 font-medium">Ofertado en vacantes</p>
                        </div>
                        <!-- KPI 3 -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group">
                            <div class="absolute right-0 bottom-0 opacity-10 group-hover:opacity-20 transition-opacity transform group-hover:scale-110 duration-300">
                                <i class="fas fa-stopwatch text-8xl text-blue-500 -mr-4 -mb-4"></i>
                            </div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Tiempo Contratacion</h3>
                            <div class="text-3xl font-extrabold text-gray-800 mb-1" id="kpi-tiempo">-- dias</div>
                            <p class="text-xs text-gray-500 font-medium">Proceso promedio</p>
                        </div>
                        <!-- KPI 4 -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group">
                            <div class="absolute right-0 bottom-0 opacity-10 group-hover:opacity-20 transition-opacity transform group-hover:scale-110 duration-300">
                                <i class="fas fa-building text-8xl text-purple-500 -mr-4 -mb-4"></i>
                            </div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Empresa Top</h3>
                            <div class="text-xl font-extrabold text-gray-800 mb-1 truncate" id="kpi-empresa-top">--</div>
                            <p class="text-xs text-gray-500 font-medium" id="kpi-empresa-top-detalle">Vacantes publicadas</p>
                        </div>
                    </div>

                    <!-- Graficas -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-sm font-bold text-gray-600 mb-4 text-center">Carreras con Mayor Contratacion</h3>
                            <div class="chart-mobile-frame h-64">
                                <canvas id="graficaCarreras"></canvas>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-sm font-bold text-gray-600 mb-4 text-center">Empresas Mas Activas</h3>
                            <div class="chart-mobile-frame h-64">
                                <canvas id="graficaEmpresas"></canvas>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2 xl:col-span-1">
                            <h3 class="text-sm font-bold text-gray-600 mb-4 text-center">Tipos de Vacantes</h3>
                            <div class="chart-mobile-frame h-64">
                                <canvas id="graficaVacantes"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PESTANA 2: ALTA DE REPRESENTANTES -->
                <div id="tab-alta" class="tab-content hidden fade-in">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/50">
                            <h2 class="text-xl font-bold text-uptex-green">Registrar Representante Corporativo</h2>
                            <p class="text-sm text-gray-500 mt-1">Crea una cuenta institucional para el enlace de una empresa aliada.</p>
                        </div>
                        <form id="formAltaRep" class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="rep_nombre" class="block text-sm font-semibold text-gray-700 mb-1">Nombre de Contacto</label>
                                    <input type="text" id="rep_nombre" required placeholder="Ej. Dr. Armando Silva" 
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-uptex-green focus:border-uptex-green outline-none transition-all shadow-sm">
                                </div>
                                <div>
                                    <label for="rep_correo" class="block text-sm font-semibold text-gray-700 mb-1">Correo de Contacto Oficial</label>
                                    <input type="email" id="rep_correo" required placeholder="enlace@empresa.com" 
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-uptex-green focus:border-uptex-green outline-none transition-all shadow-sm">
                                </div>
                                <div>
                                    <label for="rep_contrasena" class="block text-sm font-semibold text-gray-700 mb-1">Contrasena Temporal</label>
                                    <input type="password" id="rep_contrasena" required placeholder="Minimo 8 caracteres" minlength="6"
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-uptex-green focus:border-uptex-green outline-none transition-all shadow-sm">
                                </div>
                                <div>
                                    <label for="rep_empresa" class="block text-sm font-semibold text-gray-700 mb-1">Empresa Asignada</label>
                                    <select id="rep_empresa" required 
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-uptex-green focus:border-uptex-green outline-none transition-all shadow-sm bg-white">
                                        <option value="">Selecciona la empresa...</option>
                                        <option value="__nueva__">Nueva empresa</option>
                                        <!-- Opciones generadas por JS -->
                                    </select>
                                </div>
                                <div id="grupo-nueva-empresa" class="hidden md:col-span-2">
                                    <label for="rep_empresa_nombre" class="block text-sm font-semibold text-gray-700 mb-1">Nombre de la Nueva Empresa</label>
                                    <input type="text" id="rep_empresa_nombre" placeholder="Ej. Industrias del Valle S.A. de C.V."
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-uptex-green focus:border-uptex-green outline-none transition-all shadow-sm">
                                </div>
                                <div class="md:col-span-2 pt-4 border-t border-gray-100 mt-2">
                                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-uptex-green hover:bg-green-800 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition-all focus:ring-4 focus:ring-green-100 flex justify-center items-center gap-2">
                                        <i class="fas fa-save"></i> Dar de Alta Cuenta
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- PESTANA 3: CONTROL EMPRESAS -->
                <div id="tab-empresas" class="tab-content hidden fade-in">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center flex-wrap gap-3">
                            <div>
                                <h2 class="text-xl font-bold text-uptex-green">Representantes de Empresas</h2>
                                <p class="text-sm text-gray-500 mt-1">Gestion de cuentas activas de reclutadores.</p>
                            </div>
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" placeholder="Buscar..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none text-sm w-full sm:w-64">
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-600 text-sm border-b border-gray-200">
                                        <th class="py-3 px-6 font-semibold">ID</th>
                                        <th class="py-3 px-6 font-semibold">Nombre del Contacto</th>
                                        <th class="py-3 px-6 font-semibold">Email Corporativo</th>
                                        <th class="py-3 px-6 font-semibold">Rol</th>
                                        <th class="py-3 px-6 font-semibold text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-empresas-body" class="text-sm divide-y divide-gray-100">
                                    <!-- Contenido dinamico -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- PESTANA 4: CONTROL EGRESADOS -->
                <div id="tab-egresados" class="tab-content hidden fade-in">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center flex-wrap gap-3">
                            <div>
                                <h2 class="text-xl font-bold text-uptex-green">Cuentas de Egresados</h2>
                                <p class="text-sm text-gray-500 mt-1">Administracion de perfiles de alumnos titulados.</p>
                            </div>
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" placeholder="Buscar egresado..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none text-sm w-full sm:w-64">
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-600 text-sm border-b border-gray-200">
                                        <th class="py-3 px-6 font-semibold">Matricula/ID</th>
                                        <th class="py-3 px-6 font-semibold">Nombre Completo</th>
                                        <th class="py-3 px-6 font-semibold">Email Institucional</th>
                                        <th class="py-3 px-6 font-semibold">Estatus</th>
                                        <th class="py-3 px-6 font-semibold text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-egresados-body" class="text-sm divide-y divide-gray-100">
                                    <!-- Contenido dinamico -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- PESTANA 5: USUARIOS ACEPTADOS -->
                <div id="tab-usuarios-aceptados" class="tab-content hidden fade-in">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/50">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <div>
                                    <h2 class="text-xl font-bold text-uptex-green">Usuarios Aceptados</h2>
                                    <p class="text-sm text-gray-500 mt-1">Seguimiento de egresados por estatus de postulacion.</p>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full lg:w-auto">
                                    <div class="relative">
                                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input id="buscador-usuarios-aceptados" type="text" placeholder="Buscar por nombre..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none text-sm w-full lg:w-72">
                                    </div>
                                    <select id="filtro-usuarios-estatus" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none text-sm bg-white w-full lg:w-56">
                                        <option value="">Todos los estatus</option>
                                        <option value="Contratado">Contratado</option>
                                        <option value="En Entrevista">Mandado a cita</option>
                                        <option value="Rechazado">Rechazado</option>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Sin proceso">Sin proceso</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-600 text-sm border-b border-gray-200">
                                        <th class="py-3 px-6 font-semibold">ID</th>
                                        <th class="py-3 px-6 font-semibold">Nombre</th>
                                        <th class="py-3 px-6 font-semibold">Correo</th>
                                        <th class="py-3 px-6 font-semibold">Carrera</th>
                                        <th class="py-3 px-6 font-semibold">Estatus</th>
                                        <th class="py-3 px-6 font-semibold">Vacante</th>
                                        <th class="py-3 px-6 font-semibold">Empresa</th>
                                        <th class="py-3 px-6 font-semibold">Actualizacion</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-usuarios-aceptados-body" class="text-sm divide-y divide-gray-100">
                                    <!-- Contenido dinamico -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Contenedor de Toasts (Reemplazo de alert) -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3"></div>

    <!-- Modal de Edicion (Reemplazo de prompt) -->
    <div id="edit-modal" class="fixed inset-0 bg-gray-900/60 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-11/12 max-w-md overflow-hidden transform scale-95 transition-transform duration-300" id="edit-modal-content">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Modificar Usuario</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <form id="form-editar">
                    <input type="hidden" id="edit-id">
                    <input type="hidden" id="edit-tipo">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre Completo</label>
                        <input type="text" id="edit-nombre" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Correo Electronico</label>
                        <input type="email" id="edit-correo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uptex-green outline-none">
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-uptex-green hover:bg-green-800 text-white rounded-lg font-medium shadow-md transition-colors">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let charts = {};
        let datosDashboardAdmin = null;
        let usuariosAceptadosAdmin = [];
        const tokenAcceso = sessionStorage.getItem('token_acceso');
        document.getElementById('nombre-admin').innerText = sessionStorage.getItem('usuario_nombre') || 'Administrador';

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
                const mensaje = datos.message || datos.mensaje || 'No se pudo completar la operacion.';
                throw new Error(typeof mensaje === 'string' ? mensaje : JSON.stringify(mensaje));
            }

            return datos;
        }

        // Al cargar DOM
        document.addEventListener('DOMContentLoaded', () => {
            cargarSelectEmpresas();
            document.getElementById('rep_empresa').addEventListener('change', alternarNuevaEmpresa);
            document.getElementById('buscador-usuarios-aceptados')?.addEventListener('input', renderUsuariosAceptados);
            document.getElementById('filtro-usuarios-estatus')?.addEventListener('change', renderUsuariosAceptados);
            cargarDatosReales(); // Carga dashboard inicial
        });

        // --- SISTEMA DE UI: SIDEBAR Y TABS ---
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const texts = document.querySelectorAll('.menu-text');
            const title = document.getElementById('sidebar-title');

            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('sidebar-mobile-open');
                return;
            }
            
            if (sidebar.classList.contains('w-64')) {
                sidebar.classList.replace('w-64', 'w-16');
                texts.forEach(t => t.classList.add('hidden'));
                title.classList.add('opacity-0');
            } else {
                sidebar.classList.replace('w-16', 'w-64');
                setTimeout(() => {
                    texts.forEach(t => t.classList.remove('hidden'));
                    title.classList.remove('opacity-0');
                }, 150); // delay para sincronizar con transicion
            }
        }

        function cambiarPestana(tabId, btnElement) {
            // Ocultar todas las pestanas
            document.querySelectorAll('.tab-content').forEach(p => p.classList.add('hidden'));
            // Resetear estilos de botones
            document.querySelectorAll('.menu-btn').forEach(btn => {
                btn.className = "menu-btn w-full flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-uptex-green border-r-4 border-transparent transition-colors";
            });
            
            // Mostrar pestana activa y estilizar boton
            document.getElementById(tabId).classList.remove('hidden');
            if(btnElement) {
                btnElement.className = "menu-btn w-full flex items-center px-4 py-3 text-sm font-medium transition-colors bg-uptex-lightgreen text-uptex-green border-r-4 border-uptex-green";
            }

            // Cargar datos segun pestana
            if(tabId === 'tab-dashboard') cargarDatosReales();
            if(tabId === 'tab-empresas') cargarTablaEmpresas();
            if(tabId === 'tab-egresados') cargarTablaEgresados();
            if(tabId === 'tab-usuarios-aceptados') cargarUsuariosAceptados();
            
            // Auto colapsar en moviles
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.remove('sidebar-mobile-open');
            }
        }

        // --- SISTEMA DE UI: TOASTS Y MODALES ---
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            // Configurar colores segun tipo
            let bgClass = type === 'success' ? 'bg-white border-l-4 border-green-500 text-gray-700' :
                          type === 'error' ? 'bg-white border-l-4 border-red-500 text-gray-700' :
                          'bg-white border-l-4 border-blue-500 text-gray-700';
            let icon = type === 'success' ? '<i class="fas fa-check-circle text-green-500"></i>' :
                       type === 'error' ? '<i class="fas fa-exclamation-circle text-red-500"></i>' :
                       '<i class="fas fa-info-circle text-blue-500"></i>';

            toast.className = `flex items-center gap-3 px-4 py-3 shadow-lg rounded-lg min-w-[250px] toast-enter ${bgClass}`;
            toast.innerHTML = `
                <div class="text-lg">${icon}</div>
                <div class="font-medium text-sm flex-1">${message}</div>
                <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                if(container.contains(toast)) {
                    toast.classList.replace('toast-enter', 'toast-leave');
                    setTimeout(() => toast.remove(), 300);
                }
            }, 4000);
        }

        function openModal(id, nombre, correo, tipo) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nombre').value = nombre;
            document.getElementById('edit-correo').value = correo;
            document.getElementById('edit-tipo').value = tipo; // 'empresa' o 'egresado'
            
            const modal = document.getElementById('edit-modal');
            const content = document.getElementById('edit-modal-content');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.replace('scale-95', 'scale-100');
            }, 10);
        }

        function abrirModalDesdeBoton(boton) {
            openModal(boton.dataset.id, boton.dataset.nombre, boton.dataset.correo, boton.dataset.tipo);
        }

        function closeModal() {
            const modal = document.getElementById('edit-modal');
            const content = document.getElementById('edit-modal-content');
            
            content.classList.replace('scale-100', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        document.getElementById('form-editar').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('edit-id').value;
            const nuevoNombre = document.getElementById('edit-nombre').value;
            const nuevoCorreo = document.getElementById('edit-correo').value;
            const tipo = document.getElementById('edit-tipo').value;

            try {
                await apiFetch(`/api/admin/usuarios/${id}`, {
                    method: 'PUT',
                    body: JSON.stringify({
                        nombre: nuevoNombre,
                        correo: nuevoCorreo
                    })
                });

                showToast(`Usuario ${nuevoNombre} actualizado correctamente.`, 'success');
                closeModal();

                if(tipo === 'empresa') cargarTablaEmpresas();
                else cargarTablaEgresados();
            } catch (error) {
                showToast(error.message, 'error');
            }
        });

        // --- Logica de Datos y Graficas ---
        async function cargarDatosReales() {
            try {
                const datos = await apiFetch('/api/dashboard/metricas');
                datosDashboardAdmin = datos;

                document.getElementById('kpi-empleabilidad').textContent = `${datos.kpis?.empleabilidad ?? 0}%`;
                document.getElementById('kpi-tiempo').textContent = `${datos.kpis?.tiempo_promedio ?? 0} dias`;
                document.getElementById('kpi-salario').textContent = `$ ${datos.kpis?.salario_promedio ?? '0.00'}`;
                document.getElementById('kpi-empresa-top').textContent = datos.kpis?.empresa_top_nombre ?? 'Sin datos';
                document.getElementById('kpi-empresa-top-detalle').textContent = `${datos.kpis?.empresa_top_total ?? 0} vacantes publicadas`;

                dibujarGraficas(datos.graficas || {});
            } catch (error) {
                showToast(error.message || 'No se pudieron cargar las metricas reales.', 'error');
            }
        }

        function crearGrafica(canvasId, tipo, datos, colorPrincipal) {
            const contexto = document.getElementById(canvasId).getContext('2d');
            if (charts[canvasId]) charts[canvasId].destroy();
            Chart.defaults.font.family = 'Inter, Segoe UI, sans-serif';
            Chart.defaults.color = '#68746f';

            const valores = Array.isArray(datos.valores) ? datos.valores : [];
            const etiquetas = Array.isArray(datos.etiquetas) ? datos.etiquetas : [];
            const colors = Array.isArray(colorPrincipal) ? colorPrincipal :
                valores.map((_, i) => i === 0 ? colorPrincipal : colorPrincipal + '80');

            charts[canvasId] = new Chart(contexto, {
                type: tipo,
                data: {
                    labels: etiquetas,
                    datasets: [{
                        label: 'Total',
                        data: valores,
                        backgroundColor: colors,
                        borderColor: tipo === 'doughnut' ? '#fff' : colorPrincipal,
                        borderWidth: tipo === 'doughnut' ? 3 : 0,
                        borderRadius: tipo === 'bar' ? 12 : 0,
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
                            display: tipo === 'doughnut',
                            position: 'bottom',
                            labels: { usePointStyle: true, boxWidth: 8, padding: 18 }
                        },
                        tooltip: {
                            backgroundColor: '#24342d',
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: false,
                            titleFont: { size: 13, family: 'Inter' },
                            bodyFont: { size: 14, family: 'Inter', weight: 'bold' }
                        }
                    },
                    scales: tipo === 'bar' ? {
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(104,116,111,0.14)' }, border: { display: false } },
                        x: { grid: { display: false }, border: { display: false } }
                    } : {}
                }
            });
        }

        function dibujarGraficas(graficasData) {
            const sinDatos = { etiquetas: ['Sin datos'], valores: [0] };
            crearGrafica('graficaCarreras', 'bar', graficasData.carreras || sinDatos, '#006837');
            crearGrafica('graficaEmpresas', 'bar', graficasData.empresas || sinDatos, '#5bc0de');
            crearGrafica('graficaVacantes', 'doughnut', graficasData.vacantes || sinDatos, ['#006837', '#28a745', '#C1272D', '#ffc107', '#17a2b8']);
            redimensionarGraficasAdmin();
        }

        function redimensionarGraficasAdmin() {
            requestAnimationFrame(() => {
                setTimeout(() => {
                    Object.values(charts).forEach(chart => chart?.resize());
                }, 120);
            });
        }

        window.addEventListener('resize', redimensionarGraficasAdmin);
        window.addEventListener('orientationchange', redimensionarGraficasAdmin);

        async function exportarPDFAdmin() {
            if (!datosDashboardAdmin) {
                showToast('Espera a que carguen los datos del dashboard.', 'info');
                return;
            }

            const boton = document.getElementById('btnExportarPDFAdmin');
            const textoOriginal = boton.innerHTML;
            boton.disabled = true;
            boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando PDF';

            try {
                if (!window.html2canvas || !window.jspdf?.jsPDF) {
                    throw new Error('No se cargaron las librerias para generar PDF.');
                }

                const contenido = document.querySelector('#tab-dashboard');
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

                pdf.save(`dashboard-admin-${fechaArchivoAdmin()}.pdf`);
                showToast('PDF descargado correctamente.', 'success');
            } catch (error) {
                console.error('Error al exportar PDF:', error);
                showToast('No se pudo generar el PDF. Verifica tu conexion e intenta de nuevo.', 'error');
            } finally {
                boton.disabled = false;
                boton.innerHTML = textoOriginal;
            }
        }

        function exportarExcelAdmin() {
            if (!datosDashboardAdmin) {
                showToast('Espera a que carguen los datos del dashboard.', 'info');
                return;
            }

            const boton = document.getElementById('btnExportarExcelAdmin');
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
                    <h1>Dashboard Administrador UPTeX</h1>
                    <p>Generado: ${escaparHtml(new Date().toLocaleString('es-MX'))}</p>
                    ${tablaExcelAdmin('Metricas', [
                        ['Metrica', 'Valor'],
                        ['Tasa de empleabilidad', `${datosDashboardAdmin.kpis?.empleabilidad ?? 0}%`],
                        ['Salario promedio', datosDashboardAdmin.kpis?.salario_promedio ?? 0],
                        ['Tiempo promedio de contratacion', `${datosDashboardAdmin.kpis?.tiempo_promedio ?? 0} dias`],
                        ['Empresa mas activa', datosDashboardAdmin.kpis?.empresa_top_nombre || 'Sin datos'],
                        ['Vacantes de la empresa mas activa', datosDashboardAdmin.kpis?.empresa_top_total ?? 0]
                    ])}
                    ${tablaExcelAdmin('Carreras con mayor contratacion', [['Carrera', 'Total'], ...filasGraficaAdmin(datosDashboardAdmin.graficas?.carreras)])}
                    ${tablaExcelAdmin('Empresas mas activas', [['Empresa', 'Total'], ...filasGraficaAdmin(datosDashboardAdmin.graficas?.empresas)])}
                    ${tablaExcelAdmin('Vacantes mas solicitadas', [['Vacante', 'Total'], ...filasGraficaAdmin(datosDashboardAdmin.graficas?.vacantes)])}
                </body>
                </html>
            `;

            const blob = new Blob(['\ufeff' + htmlExcel], { type: 'application/vnd.ms-excel;charset=utf-8;' });
            const enlace = document.createElement('a');
            enlace.href = URL.createObjectURL(blob);
            enlace.download = `dashboard-admin-${fechaArchivoAdmin()}.xls`;
            document.body.appendChild(enlace);
            enlace.click();
            document.body.removeChild(enlace);
            URL.revokeObjectURL(enlace.href);
            boton.disabled = false;
            boton.innerHTML = textoOriginal;
            showToast('Excel descargado correctamente.', 'success');
        }

        function filasGraficaAdmin(datos) {
            const etiquetas = Array.isArray(datos?.etiquetas) ? datos.etiquetas : ['Sin datos'];
            const valores = Array.isArray(datos?.valores) ? datos.valores : [0];
            return etiquetas.map((etiqueta, index) => [etiqueta, valores[index] ?? 0]);
        }

        function tablaExcelAdmin(titulo, filas) {
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

        function fechaArchivoAdmin() {
            return new Date().toISOString().slice(0, 10);
        }

        // --- Controladores de Formularios y Tablas ---
        async function cargarSelectEmpresas() {
            const select = document.getElementById('rep_empresa');
            select.innerHTML = '<option value="">Selecciona la empresa...</option><option value="__nueva__">Nueva empresa</option>';

            try {
                const empresas = await apiFetch('/api/empresas');
                empresas.forEach(emp => {
                    select.innerHTML += `<option value="${emp.id}">${escaparHtml(emp.nombre)}</option>`;
                });
            } catch (error) {
                showToast(error.message || 'No se pudieron cargar las empresas.', 'error');
            }
        }

        function alternarNuevaEmpresa() {
            const select = document.getElementById('rep_empresa');
            const grupoNuevaEmpresa = document.getElementById('grupo-nueva-empresa');
            const inputNuevaEmpresa = document.getElementById('rep_empresa_nombre');
            const esNuevaEmpresa = select.value === '__nueva__';

            grupoNuevaEmpresa.classList.toggle('hidden', !esNuevaEmpresa);
            inputNuevaEmpresa.required = esNuevaEmpresa;

            if (!esNuevaEmpresa) {
                inputNuevaEmpresa.value = '';
            }
        }

        document.getElementById('formAltaRep').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const textoOriginal = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registrando...';
            btn.disabled = true;

            try {
                const empresaSeleccionada = document.getElementById('rep_empresa').value;
                const payload = {
                    nombre: document.getElementById('rep_nombre').value,
                    correo: document.getElementById('rep_correo').value,
                    contrasena: document.getElementById('rep_contrasena').value
                };

                if (empresaSeleccionada === '__nueva__') {
                    payload.empresa_nombre = document.getElementById('rep_empresa_nombre').value;
                } else {
                    payload.empresa_id = empresaSeleccionada;
                }

                await apiFetch('/api/admin/crear-representante', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });

                showToast('Cuenta de representante creada con exito.', 'success');
                this.reset();
                alternarNuevaEmpresa();
                cargarSelectEmpresas();
                cargarTablaEmpresas();
            } catch (error) {
                showToast(error.message, 'error');
            } finally {
                btn.innerHTML = textoOriginal;
                btn.disabled = false;
            }
        });

        async function cargarTablaEmpresas() {
            const tbody = document.getElementById('tabla-empresas-body');
            tbody.innerHTML = '<tr><td colspan="5" class="py-6 px-6 text-center text-gray-500">Cargando empresas...</td></tr>';

            try {
                const data = await apiFetch('/api/admin/empresas');

                if (!data.length) {
                    tbody.innerHTML = '<tr><td colspan="5" class="py-6 px-6 text-center text-gray-500">No hay representantes de empresa registrados.</td></tr>';
                    return;
                }

                let html = '';
                data.forEach(user => {
                    const id = escaparHtml(user.id);
                    const nombre = escaparHtml(user.nombre);
                    const correo = escaparHtml(user.correo);
                    const rol = escaparHtml(user.rol || 'empresa');
                    html += `
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="py-3 px-6 text-gray-500 font-medium">#${id}</td>
                    <td class="py-3 px-6 text-gray-800 font-semibold">${nombre}</td>
                    <td class="py-3 px-6 text-gray-500">${correo}</td>
                    <td class="py-3 px-6"><span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">${rol}</span></td>
                    <td class="py-3 px-6 text-center">
                        <button onclick="abrirModalDesdeBoton(this)" data-id="${id}" data-nombre="${nombre}" data-correo="${correo}" data-tipo="empresa" class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors" title="Editar">
                            <i class="fas fa-pen"></i>
                        </button>
                    </td>
                </tr>`;
                });
                tbody.innerHTML = html;
            } catch (error) {
                tbody.innerHTML = '<tr><td colspan="5" class="py-6 px-6 text-center text-red-600">No se pudieron cargar las empresas.</td></tr>';
                showToast(error.message, 'error');
            }
        }

        async function cargarTablaEgresados() {
            const tbody = document.getElementById('tabla-egresados-body');
            tbody.innerHTML = '<tr><td colspan="5" class="py-6 px-6 text-center text-gray-500">Cargando egresados...</td></tr>';

            try {
                const data = await apiFetch('/api/admin/egresados');

                if (!data.length) {
                    tbody.innerHTML = '<tr><td colspan="5" class="py-6 px-6 text-center text-gray-500">No hay egresados registrados.</td></tr>';
                    return;
                }

                let html = '';
                data.forEach(user => {
                    const id = escaparHtml(user.id);
                    const nombre = escaparHtml(user.nombre);
                    const correo = escaparHtml(user.correo);
                    const estatus = escaparHtml(user.estatus || user.estado_laboral || 'Sin estatus');
                    const estadoMinusculas = estatus.toLowerCase();
                    let colorBadge = estadoMinusculas === 'contratado' ? 'bg-green-100 text-green-800' :
                                     estadoMinusculas === 'inactivo' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800';
                    html += `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 px-6 text-gray-500 font-medium">${id}</td>
                    <td class="py-3 px-6 text-gray-800 font-semibold flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">${nombre.charAt(0)}</div>
                        ${nombre}
                    </td>
                    <td class="py-3 px-6 text-gray-500">${correo}</td>
                    <td class="py-3 px-6"><span class="${colorBadge} text-xs font-semibold px-2.5 py-0.5 rounded-full">${estatus}</span></td>
                    <td class="py-3 px-6 text-center">
                        <button onclick="abrirModalDesdeBoton(this)" data-id="${id}" data-nombre="${nombre}" data-correo="${correo}" data-tipo="egresado" class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors" title="Editar">
                            <i class="fas fa-pen"></i>
                        </button>
                    </td>
                </tr>`;
                });
                tbody.innerHTML = html;
            } catch (error) {
                tbody.innerHTML = '<tr><td colspan="5" class="py-6 px-6 text-center text-red-600">No se pudieron cargar los egresados.</td></tr>';
                showToast(error.message, 'error');
            }
        }

        async function cargarUsuariosAceptados() {
            const tbody = document.getElementById('tabla-usuarios-aceptados-body');
            tbody.innerHTML = '<tr><td colspan="8" class="py-6 px-6 text-center text-gray-500">Cargando usuarios...</td></tr>';

            try {
                usuariosAceptadosAdmin = await apiFetch('/api/admin/usuarios-aceptados');
                renderUsuariosAceptados();
            } catch (error) {
                usuariosAceptadosAdmin = [];
                tbody.innerHTML = '<tr><td colspan="8" class="py-6 px-6 text-center text-red-600">No se pudieron cargar los usuarios aceptados.</td></tr>';
                showToast(error.message, 'error');
            }
        }

        function renderUsuariosAceptados() {
            const tbody = document.getElementById('tabla-usuarios-aceptados-body');
            if (!tbody) return;

            const busqueda = (document.getElementById('buscador-usuarios-aceptados')?.value || '').toLowerCase().trim();
            const estatusFiltro = document.getElementById('filtro-usuarios-estatus')?.value || '';

            const usuariosFiltrados = usuariosAceptadosAdmin.filter(usuario => {
                const coincideBusqueda = !busqueda
                    || String(usuario.nombre || '').toLowerCase().includes(busqueda)
                    || String(usuario.correo || '').toLowerCase().includes(busqueda);
                const coincideEstatus = !estatusFiltro || String(usuario.estatus || '') === estatusFiltro;

                return coincideBusqueda && coincideEstatus;
            });

            if (!usuariosFiltrados.length) {
                tbody.innerHTML = '<tr><td colspan="8" class="py-6 px-6 text-center text-gray-500">No hay usuarios que coincidan con el filtro.</td></tr>';
                return;
            }

            tbody.innerHTML = usuariosFiltrados.map(usuario => {
                const estatus = usuario.estatus || 'Sin proceso';
                const badge = claseBadgeEstatus(estatus);

                return `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 px-6 text-gray-500 font-medium">#${escaparHtml(usuario.id)}</td>
                    <td class="py-3 px-6 text-gray-800 font-semibold">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-uptex-lightgreen text-uptex-green flex items-center justify-center text-xs font-bold shrink-0">${escaparHtml(usuario.nombre || 'U').charAt(0)}</div>
                            <span class="truncate">${escaparHtml(usuario.nombre || 'Sin nombre')}</span>
                        </div>
                    </td>
                    <td class="py-3 px-6 text-gray-500">${escaparHtml(usuario.correo || 'Sin correo')}</td>
                    <td class="py-3 px-6 text-gray-600">${escaparHtml(usuario.carrera || 'Sin carrera')}</td>
                    <td class="py-3 px-6"><span class="${badge} text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap">${escaparHtml(textoEstatusAdmin(estatus))}</span></td>
                    <td class="py-3 px-6 text-gray-600">${escaparHtml(usuario.vacante || 'Sin vacante')}</td>
                    <td class="py-3 px-6 text-gray-600">${escaparHtml(usuario.empresa || 'Sin empresa')}</td>
                    <td class="py-3 px-6 text-gray-500">${escaparHtml(usuario.fecha || 'Sin fecha')}</td>
                </tr>`;
            }).join('');
        }

        function claseBadgeEstatus(estatus) {
            const estado = String(estatus || '').toLowerCase();

            if (estado === 'contratado') return 'bg-green-100 text-green-700';
            if (estado === 'en entrevista') return 'bg-blue-100 text-blue-700';
            if (estado === 'rechazado') return 'bg-red-100 text-red-700';
            if (estado === 'pendiente') return 'bg-yellow-100 text-yellow-700';
            return 'bg-gray-100 text-gray-600';
        }

        function textoEstatusAdmin(estatus) {
            return String(estatus || '').toLowerCase() === 'en entrevista' ? 'Mandado a cita' : estatus;
        }

        // Cierre de sesion
        async function cerrarSesion() {
            showToast('Cerrando sesion...', 'info');
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
        }    </script>
</body>
</html>



