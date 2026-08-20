<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPTeX - Directorio de Empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/uptex-bootstrap-theme.css') }}">
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
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--fondo-gris);
            margin: 0;
            padding: 0;
            color: var(--texto-oscuro);
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Navbar */
        .navbar {
            background-color: var(--uptex-blanco);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border-bottom: 4px solid var(--uptex-rojo);
            z-index: 10;
        }

        .navbar h1 { color: var(--uptex-verde); margin: 0; font-size: 24px; font-weight: 800; }
        .navbar .usuario-info { font-weight: 600; font-size: 14px; }
        .btn-salir { color: var(--uptex-rojo); text-decoration: none; margin-left: 15px; font-weight: bold; }

        /* Contenedor Principal */
        .main-container {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            padding: 20px 40px;
            gap: 20px;
            overflow: hidden;
        }

        /* Zona de Filtros */
        .filtros-wrapper {
            background-color: var(--uptex-blanco);
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .buscador-principal {
            display: flex;
            gap: 10px;
        }

        .buscador-principal input {
            flex-grow: 1;
            padding: 12px 15px;
            border: 1px solid var(--borde-color);
            border-radius: 5px;
            font-size: 16px;
        }

        .filtros-secundarios {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .filtros-secundarios select {
            flex: 1;
            padding: 8px;
            border: 1px solid var(--borde-color);
            border-radius: 4px;
            font-size: 14px;
            color: var(--texto-oscuro);
        }

        .btn-buscar {
            padding: 0 30px;
            background-color: var(--uptex-verde);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
        }

        /* Layout Dividido */
        .split-layout {
            display: grid;
            grid-template-columns: minmax(320px, 380px) minmax(0, 1fr);
            gap: 20px;
            flex-grow: 1;
            overflow: hidden;
            align-items: stretch;
            min-width: 0;
        }

        /* Panel Izquierdo */
        .panel-lista {
            width: 100%;
            min-width: 0;
            background: transparent;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding-right: 5px;
        }

        .empresa-item {
            background-color: var(--uptex-blanco);
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--borde-color);
            border-left: 5px solid transparent;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 0;
        }

        .empresa-item:hover { border-color: var(--uptex-verde); }
        .empresa-item.activa {
            border-left-color: var(--uptex-verde);
            box-shadow: 0 4px 10px rgba(0,104,55,0.1);
            background-color: #f9fdfa;
        }

        .empresa-icono {
            width: 50px;
            min-width: 50px;
            height: 50px;
            background-color: var(--fondo-gris);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--uptex-verde);
            font-weight: bold;
        }

        .empresa-info { min-width: 0; }
        .empresa-info h4,
        .empresa-info .sector,
        .empresa-info .ubicacion { overflow-wrap: anywhere; }

        .empresa-info h4 { margin: 0 0 5px 0; color: var(--uptex-verde); font-size: 16px; }
        .empresa-info .sector { font-size: 13px; color: var(--texto-mutado); margin-bottom: 5px; }
        .empresa-info .ubicacion { font-size: 12px; color: var(--texto-oscuro); display: flex; align-items: center; gap: 5px; }

        /* Panel Derecho */
        .panel-detalle {
            width: 100%;
            min-width: 0;
            background-color: var(--uptex-blanco);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 30px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            overflow-wrap: anywhere;
        }

        .detalle-vacio {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--texto-mutado);
            text-align: center;
        }

        .detalle-vacio i { font-size: 50px; color: #ddd; margin-bottom: 15px; }

        .detalle-header { border-bottom: 2px solid var(--fondo-gris); padding-bottom: 20px; margin-bottom: 20px; display: flex; gap: 20px; align-items: center; min-width: 0; }
        .detalle-header .logo-grande { width: 80px; min-width: 80px; height: 80px; background-color: var(--fondo-gris); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 35px; color: var(--uptex-verde); font-weight: bold; }
        .detalle-header h2 { margin: 0 0 10px 0; color: var(--texto-oscuro); font-size: 26px; overflow-wrap: anywhere; }
        
        .badges-container { display: flex; gap: 10px; flex-wrap: wrap; }
        .badge { background-color: var(--fondo-gris); padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; color: var(--texto-mutado); border: 1px solid var(--borde-color); }
        
        .detalle-seccion { margin-bottom: 25px; }
        .detalle-seccion h3 { font-size: 18px; border-bottom: 1px solid var(--borde-color); padding-bottom: 8px; margin-bottom: 15px; color: var(--texto-oscuro); }
        .detalle-seccion p { font-size: 15px; color: var(--texto-mutado); line-height: 1.6; text-align: justify; overflow-wrap: anywhere; }
        
        .contacto-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; }
        .contacto-item { display: flex; align-items: center; gap: 10px; font-size: 14px; color: var(--texto-mutado); }
        .contacto-item i { color: var(--uptex-verde); font-size: 18px; width: 20px; text-align: center; }
        .contacto-item a { color: var(--uptex-verde); text-decoration: none; }
        .contacto-item a:hover { text-decoration: underline; }

        @media (max-width: 900px) {
            body { height: auto; min-height: 100vh; overflow: auto; }
            .main-container { padding: 16px; overflow: visible; }
            .navbar { padding: 14px 16px; align-items: flex-start; gap: 10px; }
            .navbar .usuario-info { display: flex; flex-wrap: wrap; gap: 8px; }
            .split-layout { display: grid; grid-template-columns: 1fr; overflow: visible; }
            .panel-lista { width: 100%; max-height: 360px; }
            .panel-detalle { min-height: 360px; overflow: visible; }
            .filtros-secundarios, .buscador-principal { flex-direction: column; }
            .btn-buscar { padding: 12px; }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>UPTeX</h1>
        <div class="usuario-info">
            <span>Directorio de Empresas</span>
            <a href="/dashboard" class="btn-salir" style="color: var(--texto-mutado);"><i class="fas fa-arrow-left"></i> Dashboard</a>
            <a href="/" class="btn-salir" id="btnCerrarSesion"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </div>

    <div class="main-container">
        
        <div class="filtros-wrapper">
            <form id="filtrosForm">
                <div class="buscador-principal">
                    <input type="text" id="buscarInput" placeholder="Buscar empresa por nombre, sector o palabra clave...">
                    <button type="submit" class="btn-buscar"><i class="fas fa-search"></i> Buscar</button>
                </div>
                <div class="filtros-secundarios">
                    <select id="filtroSector">
                        <option value="">Todos los sectores</option>
                        <option value="Tecnologia de la Informacion">Tecnologia de la Informacion</option>
                        <option value="Manufactura">Manufactura</option>
                        <option value="Logistica y Transporte">Logistica y Transporte</option>
                        <option value="Finanzas">Finanzas</option>
                    </select>
                    <select id="filtroUbicacion">
                        <option value="">Cualquier ubicacion</option>
                        <option value="Estado de Mexico">Estado de Mexico</option>
                        <option value="CDMX">Ciudad de Mexico</option>
                        <option value="Queretaro">Queretaro</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="split-layout">
            
            <div class="panel-lista" id="listaEmpresas">
                <div style="text-align:center; padding: 20px; color: #666;">Cargando empresas...</div>
            </div>

            <div class="panel-detalle" id="detalleEmpresa">
                <div class="detalle-vacio">
                    <i class="fas fa-building"></i>
                    <h2>Selecciona una empresa</h2>
                    <p>Haz clic en una empresa de la izquierda para ver su informacion de contacto y detalles.</p>
                </div>
            </div>

        </div>
    </div>

<script>
    let empresasGlobales = [];

    function escaparHtml(valor) {
        return String(valor ?? '').replace(/[&<>"']/g, (caracter) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[caracter]));
    }

    function normalizarUrl(url) {
        const valor = String(url || '').trim();
        if (!valor) return '';
        return valor.startsWith('http') ? valor : `https://${valor}`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        cargarEmpresas();

        document.getElementById('filtrosForm').addEventListener('submit', function(e) {
            e.preventDefault();
            aplicarFiltros();
        });
    });

    async function cargarEmpresas(parametros = '') {
        const contenedorLista = document.getElementById('listaEmpresas');
        const panelDetalle = document.getElementById('detalleEmpresa');
        
        contenedorLista.innerHTML = '<div style="text-align:center; padding:20px; color: var(--texto-mutado);"><i class="fas fa-spinner fa-spin"></i> Cargando empresas reales...</div>';
        
        try {
            const url = '/api/empresas' + parametros;
            const response = await fetch(url, {
                method: 'GET',
                headers: { 
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            
            const data = await response.json();
            empresasGlobales = Array.isArray(data) ? data : (data.data || []);
            renderizarLista();

        } catch (error) {
            console.error('Error al cargar empresas desde la base de datos:', error);
            
            contenedorLista.innerHTML = `
                <div style="color: var(--uptex-rojo); padding: 20px; text-align:center; font-weight: 600;">
                    <i class="fas fa-exclamation-triangle"></i> Error al conectar con el servidor.
                </div>`;
                
            panelDetalle.innerHTML = `
                <div class="detalle-vacio">
                    <i class="fas fa-server" style="color: var(--uptex-rojo);"></i>
                    <h2 style="color: var(--uptex-rojo);">Error de Base de Datos</h2>
                    <p>No se pudieron recuperar las empresas. Asegurate de tener levantado el servidor y configurado el endpoint <strong>/api/empresas</strong>.</p>
                </div>`;
        }
    }

    function aplicarFiltros() {
        const params = new URLSearchParams();
        const busqueda = document.getElementById('buscarInput').value;
        const sector = document.getElementById('filtroSector').value;
        const ubicacion = document.getElementById('filtroUbicacion').value;

        if(busqueda) params.append('search', busqueda);
        if(sector) params.append('sector', sector);
        if(ubicacion) params.append('ubicacion', ubicacion);
        
        let query = [...params].length > 0 ? `?${params.toString()}` : '';
        cargarEmpresas(query);
    }

    function renderizarLista() {
        const contenedorLista = document.getElementById('listaEmpresas');
        contenedorLista.innerHTML = '';

        if(empresasGlobales.length === 0) {
            contenedorLista.innerHTML = '<div style="padding: 20px; text-align:center; color: var(--texto-mutado);">No se encontraron empresas registradas.</div>';
            return;
        }

        empresasGlobales.forEach((empresa, index) => {
            const item = document.createElement('div');
            item.className = 'empresa-item';
            item.id = `empresa-item-${index}`;
            item.onclick = () => mostrarDetalle(index);

            const inicial = empresa.nombre ? empresa.nombre.charAt(0).toUpperCase() : 'E';

            item.innerHTML = `
                <div class="empresa-icono">${escaparHtml(inicial)}</div>
                <div class="empresa-info">
                    <h4>${escaparHtml(empresa.nombre || 'Empresa sin nombre')}</h4>
                    <div class="sector">${escaparHtml(empresa.sector || 'Sector no especificado')}</div>
                    <div class="ubicacion"><i class="fas fa-map-marker-alt" style="color:var(--uptex-verde)"></i> ${escaparHtml(empresa.ubicacion || 'No especificada')}</div>
                </div>
            `;
            contenedorLista.appendChild(item);
        });
    }

    function mostrarDetalle(index) {
        document.querySelectorAll('.empresa-item').forEach(el => el.classList.remove('activa'));
        document.getElementById(`empresa-item-${index}`)?.classList.add('activa');

        const empresa = empresasGlobales[index];
        if (!empresa) return;
        const panelDetalle = document.getElementById('detalleEmpresa');
        const inicial = empresa.nombre ? empresa.nombre.charAt(0).toUpperCase() : 'E';
        
        // Manejo dinamico de vacantes activas (si viene como coleccion u objeto mapeado)
        const totalVacantes = empresa.vacantes_count !== undefined ? empresa.vacantes_count : (empresa.vacantes ? empresa.vacantes.length : 0);

        panelDetalle.innerHTML = `
            <div class="detalle-header">
                <div class="logo-grande">${escaparHtml(inicial)}</div>
                <div>
                    <h2>${escaparHtml(empresa.nombre || 'Empresa sin nombre')}</h2>
                    <div class="badges-container">
                        <span class="badge"><i class="fas fa-industry"></i> ${escaparHtml(empresa.sector || 'No especificado')}</span>
                        <span class="badge" style="color: ${totalVacantes > 0 ? 'var(--uptex-verde)' : 'var(--texto-mutado)'}; border-color: ${totalVacantes > 0 ? 'var(--uptex-verde)' : 'var(--borde-color)'};">
                            <i class="fas fa-briefcase"></i> ${totalVacantes} Vacantes Activas
                        </span>
                    </div>
                </div>
            </div>

            <div class="detalle-seccion">
                <h3>Acerca de la empresa</h3>
                <p>${escaparHtml(empresa.descripcion || 'Esta empresa no cuenta con una descripcion detallada en su perfil.')}</p>
            </div>

            <div class="detalle-seccion">
                <h3>Informacion de Contacto y Ubicacion</h3>
                <div class="contacto-grid">
                    <div class="contacto-item">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>${escaparHtml(empresa.direccion || empresa.ubicacion || 'Direccion no registrada')}</span>
                    </div>
                    <div class="contacto-item">
                        <i class="fas fa-phone-alt"></i>
                        <span>${escaparHtml(empresa.telefono || 'Sin numero de telefono')}</span>
                    </div>
                    <div class="contacto-item">
                        <i class="fas fa-envelope"></i>
                        ${empresa.email ? `<a href="mailto:${escaparHtml(empresa.email)}">${escaparHtml(empresa.email)}</a>` : '<span>Sin correo electronico</span>'}
                    </div>
                    <div class="contacto-item">
                        <i class="fas fa-globe"></i>
                        ${empresa.web ? `<a href="${escaparHtml(normalizarUrl(empresa.web))}" target="_blank" rel="noopener">${escaparHtml(empresa.web)}</a>` : '<span>Sin sitio web</span>'}
                    </div>
                </div>
            </div>
        `;
    }

    document.getElementById('btnCerrarSesion')?.addEventListener('click', async function(e) {
        e.preventDefault();
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
    });
</script>

</body>



