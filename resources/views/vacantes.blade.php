<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPTeX - Bolsa de Trabajo / Vacantes</title>
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
            overflow: hidden; /* Evita scroll en todo el body para manejarlo internamente */
        }

        /* Navbar */
        .navbar {
            background-color: var(--uptex-blanco);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border-bottom: 4px solid var(--uptex-rojo);
            z-index: 10;
        }

        .navbar h1 { color: var(--uptex-verde); margin: 0; font-size: 24px; font-weight: 800; }
        .navbar .usuario-info { font-weight: 600; font-size: 14px; display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
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
            margin-bottom: 15px;
        }

        .buscador-principal input {
            flex-grow: 1;
            padding: 12px 15px;
            border: 1px solid var(--borde-color);
            border-radius: 5px;
            font-size: 16px;
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

        .filtros-secundarios {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
        }

        .filtros-secundarios select {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--borde-color);
            border-radius: 4px;
            font-size: 13px;
            color: var(--texto-oscuro);
        }

        /* Layout Dividido (Master-Detail) */
        .split-layout {
            display: grid;
            grid-template-columns: minmax(320px, 380px) minmax(0, 1fr);
            gap: 20px;
            flex-grow: 1;
            overflow: hidden;
            align-items: stretch;
            min-width: 0;
        }

        /* Panel Izquierdo: Lista de Vacantes */
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

        /* Tarjetas de la lista */
        .vacante-item {
            background-color: var(--uptex-blanco);
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--borde-color);
            border-left: 5px solid transparent;
            cursor: pointer;
            transition: 0.2s;
            min-width: 0;
        }

        .vacante-item:hover { border-color: var(--uptex-verde); }
        
        .vacante-item.activa {
            border-left-color: var(--uptex-verde);
            box-shadow: 0 4px 10px rgba(0,104,55,0.1);
            background-color: #f9fdfa;
        }

        .vacante-item h4 { margin: 0 0 5px 0; color: var(--uptex-verde); font-size: 16px; }
        .vacante-item h4,
        .vacante-item .empresa-dir,
        .vacante-item .salario { overflow-wrap: anywhere; }
        .vacante-item .empresa-dir { font-size: 13px; color: var(--texto-mutado); margin-bottom: 8px; }
        .vacante-item .salario { font-weight: bold; color: var(--texto-oscuro); font-size: 14px; display: flex; align-items: center; gap: 5px; min-width: 0; }

        /* Panel Derecho: Detalles de la Vacante */
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

        .detalle-header { border-bottom: 2px solid var(--fondo-gris); padding-bottom: 20px; margin-bottom: 20px; min-width: 0; }
        .detalle-header h2 { margin: 0 0 10px 0; color: var(--texto-oscuro); font-size: 26px; overflow-wrap: anywhere; }
        .detalle-header .empresa { font-size: 18px; color: var(--uptex-verde); font-weight: 600; margin-bottom: 15px; }
        
        .badges-container { display: flex; gap: 10px; flex-wrap: wrap; }
        .badge { background-color: var(--fondo-gris); padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; color: var(--texto-mutado); border: 1px solid var(--borde-color); }
        
        .detalle-seccion { margin-bottom: 25px; }
        .detalle-seccion h3 { font-size: 18px; border-bottom: 1px solid var(--borde-color); padding-bottom: 8px; margin-bottom: 15px; color: var(--texto-oscuro); }
        .detalle-seccion p, .detalle-seccion ul { font-size: 15px; color: var(--texto-mutado); line-height: 1.6; text-align: justify; overflow-wrap: anywhere; }
        
        .btn-postular { background-color: var(--uptex-verde); color: white; border: none; padding: 12px 25px; font-size: 16px; font-weight: bold; border-radius: 5px; cursor: pointer; display: inline-block; margin-top: 10px; }
        .btn-postular:hover { background-color: #004d28; }

        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.55); display: none; align-items: center; justify-content: center; z-index: 50; padding: 20px; overflow: hidden; overscroll-behavior: contain; }
        .modal-overlay.activo { display: flex; }
        .modal-contenido { background: white; width: min(560px, 100%); height: min(720px, calc(100dvh - 32px)); max-height: calc(100dvh - 32px); border-radius: 8px; box-shadow: 0 18px 50px rgba(0,0,0,0.25); overflow: hidden; display: flex; flex-direction: column; }
        .modal-contenido form { display: flex; flex-direction: column; flex: 1 1 auto; min-height: 0; }
        .modal-header { background: var(--uptex-verde); color: white; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; }
        .modal-header h3 { margin: 0; font-size: 18px; }
        .modal-cerrar { background: transparent; border: 0; color: white; font-size: 20px; cursor: pointer; }
        .modal-body { padding: 20px; overflow-y: auto; flex: 1 1 auto; min-height: 0; -webkit-overflow-scrolling: touch; }
        .campo { margin-bottom: 14px; }
        .campo label { display: block; font-size: 13px; font-weight: 700; color: var(--texto-oscuro); margin-bottom: 6px; }
        .campo input, .campo textarea { width: 100%; box-sizing: border-box; border: 1px solid var(--borde-color); border-radius: 5px; padding: 10px 12px; font-size: 14px; font-family: inherit; }
        .campo textarea { min-height: 90px; resize: vertical; }
        .modal-acciones { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 20px; border-top: 1px solid var(--borde-color); background: #fafafa; flex: 0 0 auto; }
        .btn-secundario { background: white; color: var(--texto-mutado); border: 1px solid var(--borde-color); padding: 10px 16px; border-radius: 5px; font-weight: 700; cursor: pointer; }
        .mensaje-postulacion { margin-top: 10px; font-weight: 700; font-size: 14px; }
        .mensaje-postulacion.error { color: var(--uptex-rojo); }
        .mensaje-postulacion.exito { color: var(--uptex-verde); }
        .detalle-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 12px; }
        .detalle-dato { background: var(--fondo-gris); border: 1px solid var(--borde-color); border-radius: 8px; padding: 12px; min-width: 0; }
        .detalle-dato strong { display: block; color: var(--texto-oscuro); font-size: 12px; margin-bottom: 5px; text-transform: uppercase; }
        .detalle-dato span { display: block; color: var(--texto-mutado); font-size: 14px; overflow-wrap: anywhere; }

        @media (max-width: 900px) {
            body { height: auto; min-height: 100vh; overflow: auto; }
            .main-container { padding: 16px; overflow: visible; }
            .navbar { padding: 14px 16px; align-items: flex-start; gap: 10px; }
            .navbar h1 { font-size: 21px; }
            .btn-salir { margin-left: 0; }
            .buscador-principal { flex-direction: column; }
            .btn-buscar { padding: 12px; }
            .split-layout { display: grid; grid-template-columns: 1fr; overflow: visible; }
            .panel-lista { width: 100%; max-height: 380px; }
            .panel-detalle { min-height: 420px; overflow: visible; }
            .modal-overlay { align-items: center; padding: 12px; }
            .modal-contenido { width: 100%; height: calc(100dvh - 24px); max-height: calc(100dvh - 24px); }
            .modal-body { padding: 16px; }
            .modal-acciones { flex-direction: column-reverse; }
            .modal-acciones button { width: 100%; }
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
            <span>Bolsa de Trabajo para Egresados</span>
            <a href="/dashboard" class="btn-salir" style="color: var(--texto-mutado);"><i class="fas fa-arrow-left"></i> volver</a>
            <a href="/" class="btn-salir" id="btnCerrarSesion"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </div>

    <div class="main-container">
        
        <!-- Contenedor de Busqueda y Filtros -->
        <div class="filtros-wrapper">
            <form id="filtrosForm">
                <div class="buscador-principal">
                    <input type="text" id="buscarInput" placeholder="Buscar cargo, empresa o palabras clave...">
                    <button type="submit" class="btn-buscar"><i class="fas fa-search"></i> Buscar</button>
                </div>
                <div class="filtros-secundarios">
                    <select id="filtroOrdenar">
                        <option value="">Ordenar por...</option>
                        <option value="recientes">Mas recientes</option>
                        <option value="relevantes">Mas relevantes</option>
                        <option value="salario_alto">Mayor salario</option>
                    </select>
                    <select id="filtroFecha">
                        <option value="">Fecha de pub.</option>
                        <option value="hoy">Hoy</option>
                        <option value="semana">Ultima semana</option>
                        <option value="mes">Ultimo mes</option>
                    </select>
                    <select id="filtroCategoria">
                        <option value="">Categoria</option>
                        <option value="ti">Tecnologia / Sistemas</option>
                        <option value="admin">Administracion</option>
                        <option value="industrial">Ing. Industrial</option>
                    </select>
                    <select id="filtroLugar">
                        <option value="">Lugar de trabajo</option>
                        <option value="edomex">Estado de Mexico</option>
                        <option value="cdmx">CDMX</option>
                        <option value="extranjero">Extranjero</option>
                    </select>
                    <select id="filtroExperiencia">
                        <option value="">Experiencia</option>
                        <option value="sin">Sin experiencia</option>
                        <option value="1-3">1 a 3 anos</option>
                        <option value="mas3">Mas de 3 anos</option>
                    </select>
                    <select id="filtroSalario">
                        <option value="">Salario</option>
                        <option value="10k">Menos de $10,000</option>
                        <option value="10k-20k">$10,000 - $20,000</option>
                        <option value="20k+">Mas de $20,000</option>
                    </select>
                    <select id="filtroJornada">
                        <option value="">Jornada</option>
                        <option value="completo">Tiempo Completo</option>
                        <option value="medio">Medio Tiempo</option>
                    </select>
                    <select id="filtroContrato">
                        <option value="">Contrato</option>
                        <option value="indefinido">Indefinido</option>
                        <option value="temporal">Temporal</option>
                        <option value="honorarios">Honorarios</option>
                    </select>
                    <select id="filtroDiscapacidad">
                        <option value="">Apto Discapacidad</option>
                        <option value="si">Si</option>
                        <option value="no">No aplica</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Layout de Lista y Detalles -->
        <div class="split-layout">
            
            <!-- Lista Izquierda -->
            <div class="panel-lista" id="listaVacantes">
                <div style="text-align:center; padding: 20px; color: #666;">Cargando vacantes...</div>
            </div>

            <!-- Detalle Derecha -->
            <div class="panel-detalle" id="detalleVacante">
                <div class="detalle-vacio">
                    <i class="fas fa-briefcase"></i>
                    <h2>Selecciona una vacante</h2>
                    <p>Haz clic en una oferta de la izquierda para ver todos los detalles aqui.</p>
                </div>
            </div>

        </div>
    </div>

    <div class="modal-overlay" id="modalPostulacion">
        <div class="modal-contenido">
            <div class="modal-header">
                <h3>Postularme a la vacante</h3>
                <button type="button" class="modal-cerrar" onclick="cerrarModalPostulacion()">&times;</button>
            </div>
            <form id="formPostulacion" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="postulacion_vacante_id">
                    <div class="campo">
                        <label for="postulacion_nombre">Nombre completo</label>
                        <input type="text" id="postulacion_nombre" name="nombre" required>
                    </div>
                    <div class="campo">
                        <label for="postulacion_correo">Correo</label>
                        <input type="email" id="postulacion_correo" name="correo" required>
                    </div>
                    <div class="campo">
                        <label for="postulacion_telefono">Telefono</label>
                        <input type="text" id="postulacion_telefono" name="telefono" placeholder="Opcional">
                    </div>
                    <div class="campo">
                        <label for="postulacion_carrera">Carrera</label>
                        <input type="text" id="postulacion_carrera" name="carrera" placeholder="Ej. Ingenieria en Software">
                    </div>
                    <div class="campo">
                        <label for="postulacion_cv">CV</label>
                        <input type="file" id="postulacion_cv" name="cv" accept=".pdf,.doc,.docx" required>
                    </div>
                    <div class="campo">
                        <label for="postulacion_mensaje">Mensaje para la empresa</label>
                        <textarea id="postulacion_mensaje" name="mensaje" placeholder="Cuentale brevemente a la empresa por que te interesa la vacante."></textarea>
                    </div>
                    <div id="mensajePostulacion" class="mensaje-postulacion"></div>
                </div>
                <div class="modal-acciones">
                    <button type="button" class="btn-secundario" onclick="cerrarModalPostulacion()">Cancelar</button>
                    <button type="submit" class="btn-postular"><i class="fas fa-upload"></i> Enviar postulacion</button>
                </div>
            </form>
        </div>
    </div>

<script>
    let vacantesGlobales = [];

    document.addEventListener('DOMContentLoaded', function() {
        cargarVacantes();

        document.getElementById('filtrosForm').addEventListener('submit', function(e) {
            e.preventDefault();
            aplicarFiltros();
        });

        document.getElementById('formPostulacion').addEventListener('submit', enviarPostulacion);
    });

    function escaparHtml(valor) {
        return String(valor ?? '').replace(/[&<>"']/g, (caracter) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[caracter]));
    }

    function formatearSalario(vacante) {
        const salario = vacante.salario_ofrecido ?? vacante.salario;

        if (salario !== null && salario !== undefined && salario !== '') {
            return `$${Number(salario).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }

        return vacante.salario_comisiones || 'A convenir';
    }

    async function cargarVacantes(parametros = '') {
        const contenedorLista = document.getElementById('listaVacantes');
        contenedorLista.innerHTML = '<div style="text-align:center; padding:20px;">Cargando...</div>';
        
        try {
            const url = '/api/vacantes' + parametros;
            const response = await fetch(url, {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            
            const data = await response.json();
            vacantesGlobales = Array.isArray(data) ? data : (data.data || []);
            renderizarLista();
        } catch (error) {
            console.error('Error al cargar vacantes:', error);
            contenedorLista.innerHTML = '<div style="color: red; padding: 20px;">Error al cargar datos.</div>';
        }
    }

    function aplicarFiltros() {
        const params = new URLSearchParams();
        const busqueda = document.getElementById('buscarInput').value;
        if(busqueda) params.append('search', busqueda);
        
        const jornada = document.getElementById('filtroJornada').value;
        if(jornada) params.append('jornada', jornada);

        let query = [...params].length > 0 ? `?${params.toString()}` : '';
        cargarVacantes(query);
    }

    function renderizarLista() {
        const contenedorLista = document.getElementById('listaVacantes');
        contenedorLista.innerHTML = '';

        if(vacantesGlobales.length === 0) {
            contenedorLista.innerHTML = '<div style="padding: 20px; text-align:center;">No hay resultados.</div>';
            return;
        }

        vacantesGlobales.forEach((vacante, index) => {
            const item = document.createElement('div');
            item.className = 'vacante-item';
            item.id = `vacante-item-${index}`;
            item.onclick = () => mostrarDetalle(index);

            const direccion = vacante.direccion || vacante.ubicacion || 'Direccion no especificada';
            const salarioInfo = formatearSalario(vacante);

            item.innerHTML = `
                <h4>${escaparHtml(vacante.puesto || 'Vacante sin titulo')}</h4>
                <div class="empresa-dir"><i class="fas fa-building"></i> ${escaparHtml(vacante.empresa || 'Empresa no especificada')} <br> <i class="fas fa-map-marker-alt"></i> ${escaparHtml(direccion)}</div>
                <div class="salario"><i class="fas fa-money-bill-wave" style="color:var(--uptex-verde)"></i> ${escaparHtml(salarioInfo)}</div>
            `;
            contenedorLista.appendChild(item);
        });
    }

    function mostrarDetalle(index) {
        document.querySelectorAll('.vacante-item').forEach(el => el.classList.remove('activa'));
        document.getElementById(`vacante-item-${index}`)?.classList.add('activa');

        const vacante = vacantesGlobales[index];
        if (!vacante) return;
        const panelDetalle = document.getElementById('detalleVacante');
        const requerimientos = vacante.requerimientos || 'No se especificaron requerimientos adicionales.';
        const aptitudes = vacante.aptitudes || 'No se especificaron aptitudes adicionales.';
        const salarioInfo = formatearSalario(vacante);
        const tipoContrato = vacante.tipo_contrato || vacante.contrato || 'No especificado';
        const categoria = vacante.categoria || vacante.perfil || 'No especificada';
        const ubicacion = vacante.ubicacion || vacante.direccion || 'No especificada';
        const jornada = vacante.tipo_jornada || vacante.tipo || 'No especificada';
        const sector = vacante.sector || 'No especificado';
        const experiencia = vacante.experiencia || 'No especificada';
        const aptoDiscapacidad = vacante.apto_discapacidad ? 'Si' : 'No especificado';
        const salarioComisiones = vacante.salario_comisiones || 'No especificado';

        panelDetalle.innerHTML = `
            <div class="detalle-header">
                <h2>${escaparHtml(vacante.puesto || 'Vacante sin titulo')}</h2>
                <div class="empresa"><i class="fas fa-building"></i> ${escaparHtml(vacante.empresa || 'Empresa no especificada')}</div>
                <div class="badges-container">
                    <span class="badge"><i class="fas fa-map-marker-alt"></i> ${escaparHtml(ubicacion)}</span>
                    <span class="badge"><i class="fas fa-clock"></i> ${escaparHtml(jornada)}</span>
                    <span class="badge"><i class="fas fa-file-contract"></i> ${escaparHtml(tipoContrato)}</span>
                    <span class="badge"><i class="fas fa-money-bill"></i> ${escaparHtml(salarioInfo)}</span>
                </div>
            </div>

            <div class="detalle-seccion">
                <h3>Datos de la vacante</h3>
                <div class="detalle-grid">
                    <div class="detalle-dato">
                        <strong>Puesto</strong>
                        <span>${escaparHtml(vacante.puesto || 'No especificado')}</span>
                    </div>
                    <div class="detalle-dato">
                        <strong>Empresa</strong>
                        <span>${escaparHtml(vacante.empresa || 'Empresa no especificada')}</span>
                    </div>
                    <div class="detalle-dato">
                        <strong>Categoria</strong>
                        <span>${escaparHtml(categoria)}</span>
                    </div>
                    <div class="detalle-dato">
                        <strong>Jornada</strong>
                        <span>${escaparHtml(jornada)}</span>
                    </div>
                    <div class="detalle-dato">
                        <strong>Tipo de contrato</strong>
                        <span>${escaparHtml(tipoContrato)}</span>
                    </div>
                    <div class="detalle-dato">
                        <strong>Sueldo ofrecido</strong>
                        <span>${escaparHtml(salarioInfo)}</span>
                    </div>
                    <div class="detalle-dato">
                        <strong>Ubicacion</strong>
                        <span>${escaparHtml(ubicacion)}</span>
                    </div>
                    <div class="detalle-dato">
                        <strong>Sector</strong>
                        <span>${escaparHtml(sector)}</span>
                    </div>
                    <div class="detalle-dato">
                        <strong>Experiencia</strong>
                        <span>${escaparHtml(experiencia)}</span>
                    </div>
                    <div class="detalle-dato">
                        <strong>Apto discapacidad</strong>
                        <span>${escaparHtml(aptoDiscapacidad)}</span>
                    </div>
                    <div class="detalle-dato">
                        <strong>Salario/comisiones</strong>
                        <span>${escaparHtml(salarioComisiones)}</span>
                    </div>
                </div>
            </div>

            <div class="detalle-seccion">
                <h3>Descripcion del puesto</h3>
                <p>${escaparHtml(vacante.descripcion || 'No se especifico descripcion para esta vacante.')}</p>
            </div>

            <div class="detalle-seccion">
                <h3>Requerimientos</h3>
                <p>${escaparHtml(requerimientos)}</p>
            </div>

            <div class="detalle-seccion">
                <h3>Aptitudes asociadas</h3>
                <p>${escaparHtml(aptitudes)}</p>
            </div>

            <div style="margin-top: auto; border-top: 1px solid #eee; padding-top: 20px;">
                <button class="btn-postular" onclick="postularse(${vacante.id})"><i class="fas fa-paper-plane"></i> Postularme a esta vacante</button>
            </div>
        `;
    }

    function postularse(id) {
        const token = sessionStorage.getItem('token_acceso');
        const rol = sessionStorage.getItem('usuario_rol');

        if (!token || !rol || rol.toLowerCase() !== 'egresado') {
            window.location.replace('/');
            return;
        }

        document.getElementById('postulacion_vacante_id').value = id;
        document.getElementById('postulacion_nombre').value = sessionStorage.getItem('usuario_nombre') || '';
        document.getElementById('postulacion_correo').value = sessionStorage.getItem('usuario_correo') || '';
        document.getElementById('postulacion_telefono').value = '';
        document.getElementById('postulacion_carrera').value = '';
        document.getElementById('postulacion_cv').value = '';
        document.getElementById('postulacion_mensaje').value = '';
        mostrarMensajePostulacion('', '');
        document.getElementById('modalPostulacion').classList.add('activo');
    }

    function cerrarModalPostulacion() {
        document.getElementById('modalPostulacion').classList.remove('activo');
    }

    function mostrarMensajePostulacion(mensaje, tipo) {
        const contenedor = document.getElementById('mensajePostulacion');
        contenedor.textContent = mensaje;
        contenedor.className = tipo ? `mensaje-postulacion ${tipo}` : 'mensaje-postulacion';
    }

    async function enviarPostulacion(e) {
        e.preventDefault();
        const token = sessionStorage.getItem('token_acceso');
        const vacanteId = document.getElementById('postulacion_vacante_id').value;
        const form = document.getElementById('formPostulacion');
        const boton = form.querySelector('button[type="submit"]');
        const textoOriginal = boton.innerHTML;

        boton.disabled = true;
        boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        mostrarMensajePostulacion('', '');

        try {
            const formData = new FormData(form);
            const response = await fetch(`/api/vacantes/${vacanteId}/postular`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                const errores = data.errors ? Object.values(data.errors).flat().join(' ') : null;
                throw new Error(errores || data.mensaje || 'No se pudo enviar la postulacion.');
            }

            mostrarMensajePostulacion(data.mensaje || 'Postulacion enviada correctamente.', 'exito');
            setTimeout(cerrarModalPostulacion, 1200);
        } catch (error) {
            mostrarMensajePostulacion(error.message, 'error');
        } finally {
            boton.disabled = false;
            boton.innerHTML = textoOriginal;
        }
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
</html>



