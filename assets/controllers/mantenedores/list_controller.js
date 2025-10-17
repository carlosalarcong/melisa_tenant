import { Controller } from "@hotwired/stimulus";

/**
 * Controlador para el listado inteligente de mantenedores
 * Maneja búsqueda, ordenamiento, paginación y acciones CRUD
 */
export default class extends Controller {
    static targets = [
        "searchInput", "tableBody", "loadingSpinner", "emptyState",
        "pagination", "paginationInfo", "limitSelect", "totalCount", "lastUpdate"
    ];
    
    static values = {
        apiUrl: String,
        createUrl: String,
        updateUrl: String,
        deleteUrl: String,
        showUrl: String,
        csrfToken: String
    };

    connect() {
        this.debug('List controller conectado');
        
        // Estado inicial
        this.currentPage = 1;
        this.currentLimit = 25;
        this.currentSearch = '';
        this.currentOrderBy = 'id';
        this.currentOrderDir = 'ASC';
        this.searchTimeout = null;
        
        // Configuración
        this.searchDebounceTime = 300; // ms
        
        // Cargar datos iniciales
        this.loadData();
        
        // Configurar actualizaciones automáticas si es necesario
        this.setupAutoRefresh();
        
        // Escuchar eventos de otros controladores
        this.setupEventListeners();
    }

    /**
     * Realiza búsqueda con debounce
     */
    search(event) {
        const query = event.target.value.trim();
        
        // Limpiar timeout anterior
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        
        // Configurar nuevo timeout
        this.searchTimeout = setTimeout(() => {
            this.debug('Ejecutando búsqueda:', query);
            this.currentSearch = query;
            this.currentPage = 1; // Volver a la primera página
            this.loadData();
        }, this.searchDebounceTime);
    }

    /**
     * Maneja el ordenamiento por columna
     */
    sort(event) {
        event.preventDefault();
        
        const column = event.currentTarget.dataset.column;
        if (!column) return;
        
        this.debug('Ordenando por columna:', column);
        
        // Cambiar dirección si es la misma columna
        if (this.currentOrderBy === column) {
            this.currentOrderDir = this.currentOrderDir === 'ASC' ? 'DESC' : 'ASC';
        } else {
            this.currentOrderBy = column;
            this.currentOrderDir = 'ASC';
        }
        
        // Actualizar indicadores visuales
        this.updateSortIndicators();
        
        // Recargar datos
        this.loadData();
    }

    /**
     * Cambia la cantidad de elementos por página
     */
    changeLimit(event) {
        this.currentLimit = parseInt(event.target.value);
        this.currentPage = 1; // Volver a la primera página
        
        this.debug('Cambiando límite a:', this.currentLimit);
        
        // Guardar preferencia
        localStorage.setItem('mantenedor_limit', this.currentLimit);
        
        this.loadData();
    }

    /**
     * Cambia de página
     */
    changePage(event) {
        event.preventDefault();
        
        const page = parseInt(event.currentTarget.dataset.page);
        if (!page || page === this.currentPage) return;
        
        this.debug('Cambiando a página:', page);
        this.currentPage = page;
        this.loadData();
    }

    /**
     * Refresca los datos
     */
    refresh(event) {
        event?.preventDefault();
        this.debug('Refrescando datos');
        this.loadData();
    }

    /**
     * Exporta los datos (implementación básica)
     */
    exportData(event) {
        event?.preventDefault();
        
        this.debug('Exportando datos');
        
        // Construir URL con parámetros actuales
        const params = new URLSearchParams({
            search: this.currentSearch,
            orderBy: this.currentOrderBy,
            orderDir: this.currentOrderDir,
            export: 'csv' // o 'excel'
        });
        
        const exportUrl = `${this.apiUrlValue}?${params.toString()}`;
        
        // Abrir en nueva ventana o descargar
        window.open(exportUrl, '_blank');
    }

    /**
     * Abre modal para editar una entidad
     */
    editEntity(event) {
        event.preventDefault();
        
        const entityId = event.currentTarget.dataset.entityId;
        if (!entityId) {
            this.showError('ID de entidad no encontrado');
            return;
        }
        
        this.debug('Editando entidad ID:', entityId);
        
        // Disparar evento para que el modal controller lo maneje
        this.dispatch('edit', { 
            detail: { entityId: entityId }
        });
    }

    /**
     * Confirma y elimina una entidad
     */
    deleteEntity(event) {
        event.preventDefault();
        
        const entityId = event.currentTarget.dataset.entityId;
        if (!entityId) {
            this.showError('ID de entidad no encontrado');
            return;
        }
        
        this.debug('Iniciando eliminación de entidad ID:', entityId);
        
        // Guardar ID para confirmación
        this.entityToDelete = entityId;
        
        // Mostrar modal de confirmación
        this.showDeleteConfirmation();
    }

    /**
     * Confirma la eliminación después del modal
     */
    async confirmDelete(event) {
        event?.preventDefault();
        
        if (!this.entityToDelete) {
            this.showError('No hay entidad seleccionada para eliminar');
            return;
        }
        
        try {
            this.debug('Confirmando eliminación de entidad ID:', this.entityToDelete);
            
            const deleteUrl = this.deleteUrlValue.replace('ID_PLACEHOLDER', this.entityToDelete);
            
            const formData = new FormData();
            formData.append('_token', this.csrfTokenValue);
            formData.append('_method', 'DELETE');
            
            const response = await fetch(deleteUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                this.showSuccess(data.message || 'Registro eliminado exitosamente');
                this.hideDeleteConfirmation();
                this.loadData(); // Recargar lista
            } else {
                this.showError(data.message || 'Error al eliminar el registro');
            }
            
        } catch (error) {
            this.debug('Error eliminando entidad:', error);
            this.showError('Error de conexión al eliminar el registro');
        } finally {
            this.entityToDelete = null;
        }
    }

    /**
     * Carga los datos desde la API
     */
    async loadData() {
        try {
            this.showLoading();
            
            // Construir parámetros de consulta
            const params = new URLSearchParams({
                page: this.currentPage,
                limit: this.currentLimit,
                search: this.currentSearch,
                orderBy: this.currentOrderBy,
                orderDir: this.currentOrderDir
            });
            
            const url = `${this.apiUrlValue}?${params.toString()}`;
            
            this.debug('Cargando datos desde:', url);
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.renderData(data);
                this.updatePagination(data.pagination);
                this.updateInfo(data.pagination);
                this.updateLastUpdate();
            } else {
                throw new Error(data.message || 'Error en la respuesta del servidor');
            }
            
        } catch (error) {
            this.debug('Error cargando datos:', error);
            this.showError('Error al cargar los datos');
            this.showEmptyState();
        } finally {
            this.hideLoading();
        }
    }

    /**
     * Renderiza los datos en la tabla
     */
    renderData(data) {
        if (!data.data || data.data.length === 0) {
            this.showEmptyState();
            return;
        }
        
        this.hideEmptyState();
        
        const config = window.MantenedorConfig;
        const rows = data.data.map(item => this.renderRow(item, config)).join('');
        
        this.tableBodyTarget.innerHTML = rows;
        
        // Agregar animación de entrada
        this.tableBodyTarget.classList.add('fade-in');
    }

    /**
     * Renderiza una fila de la tabla
     */
    renderRow(item, config) {
        const columns = config.columns.map(column => {
            let value = item[column.key] || '';
            
            // Formatear según el tipo de columna
            if (column.type === 'boolean') {
                value = this.formatBoolean(value);
            } else if (column.key.includes('_at')) {
                value = this.formatDate(value);
            }
            
            return `<td>${value}</td>`;
        }).join('');
        
        const actions = this.renderActions(item.id);
        
        return `<tr data-entity-id="${item.id}">${columns}${actions}</tr>`;
    }

    /**
     * Renderiza las acciones de una fila
     */
    renderActions(entityId) {
        return `
            <td>
                <button class="btn-action btn-edit" 
                        data-entity-id="${entityId}"
                        data-action="click->list#editEntity"
                        title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-action btn-delete" 
                        data-entity-id="${entityId}"
                        data-action="click->list#deleteEntity"
                        title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
    }

    /**
     * Actualiza la paginación
     */
    updatePagination(pagination) {
        if (!pagination) return;
        
        const { page, pages, total } = pagination;
        
        let paginationHtml = '';
        
        // Botón anterior
        paginationHtml += `
            <li class="page-item ${page <= 1 ? 'disabled' : ''}">
                <a class="page-link" 
                   data-page="${page - 1}" 
                   data-action="click->list#changePage">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;
        
        // Números de página
        const startPage = Math.max(1, page - 2);
        const endPage = Math.min(pages, page + 2);
        
        if (startPage > 1) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" data-page="1" data-action="click->list#changePage">1</a>
                </li>
            `;
            if (startPage > 2) {
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `
                <li class="page-item ${i === page ? 'active' : ''}">
                    <a class="page-link" data-page="${i}" data-action="click->list#changePage">${i}</a>
                </li>
            `;
        }
        
        if (endPage < pages) {
            if (endPage < pages - 1) {
                paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" data-page="${pages}" data-action="click->list#changePage">${pages}</a>
                </li>
            `;
        }
        
        // Botón siguiente
        paginationHtml += `
            <li class="page-item ${page >= pages ? 'disabled' : ''}">
                <a class="page-link" 
                   data-page="${page + 1}" 
                   data-action="click->list#changePage">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
        
        this.paginationTarget.innerHTML = paginationHtml;
    }

    /**
     * Actualiza la información de paginación
     */
    updateInfo(pagination) {
        if (!pagination) return;
        
        const { showing, total } = pagination;
        
        if (this.hasPaginationInfoTarget) {
            this.paginationInfoTarget.textContent = 
                `Mostrando ${showing.from}-${showing.to} de ${showing.total} registros`;
        }
        
        if (this.hasTotalCountTarget) {
            this.totalCountTarget.textContent = total;
        }
    }

    /**
     * Actualiza la hora de última actualización
     */
    updateLastUpdate() {
        if (this.hasLastUpdateTarget) {
            const now = new Date();
            this.lastUpdateTarget.textContent = now.toLocaleTimeString();
        }
    }

    /**
     * Actualiza los indicadores de ordenamiento
     */
    updateSortIndicators() {
        // Limpiar indicadores previos
        const headers = this.element.querySelectorAll('th.sortable');
        headers.forEach(th => {
            th.classList.remove('sorting-asc', 'sorting-desc');
        });
        
        // Agregar indicador actual
        const currentHeader = this.element.querySelector(`th[data-column="${this.currentOrderBy}"]`);
        if (currentHeader) {
            currentHeader.classList.add(`sorting-${this.currentOrderDir.toLowerCase()}`);
        }
    }

    /**
     * Muestra el estado de carga
     */
    showLoading() {
        if (this.hasLoadingSpinnerTarget) {
            this.loadingSpinnerTarget.classList.remove('d-none');
        }
        this.hideEmptyState();
    }

    /**
     * Oculta el estado de carga
     */
    hideLoading() {
        if (this.hasLoadingSpinnerTarget) {
            this.loadingSpinnerTarget.classList.add('d-none');
        }
    }

    /**
     * Muestra el estado vacío
     */
    showEmptyState() {
        if (this.hasEmptyStateTarget) {
            this.emptyStateTarget.classList.remove('d-none');
        }
        this.tableBodyTarget.innerHTML = '';
        
        if (this.hasPaginationTarget) {
            this.paginationTarget.innerHTML = '';
        }
    }

    /**
     * Oculta el estado vacío
     */
    hideEmptyState() {
        if (this.hasEmptyStateTarget) {
            this.emptyStateTarget.classList.add('d-none');
        }
    }

    /**
     * Muestra el modal de confirmación de eliminación
     */
    showDeleteConfirmation() {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    /**
     * Oculta el modal de confirmación de eliminación
     */
    hideDeleteConfirmation() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        if (modal) {
            modal.hide();
        }
    }

    /**
     * Formatea valores booleanos
     */
    formatBoolean(value) {
        const isTrue = value === true || value === 1 || value === '1';
        const badgeClass = isTrue ? 'badge-activo' : 'badge-inactivo';
        const text = isTrue ? 'Activo' : 'Inactivo';
        return `<span class="badge ${badgeClass}">${text}</span>`;
    }

    /**
     * Formatea fechas
     */
    formatDate(dateString) {
        if (!dateString) return '';
        
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        } catch (e) {
            return dateString;
        }
    }

    /**
     * Configura la actualización automática
     */
    setupAutoRefresh() {
        // Actualizar cada 30 segundos si la configuración lo permite
        if (window.MantenedorConfig?.autoRefresh) {
            setInterval(() => {
                this.refresh();
            }, 30000);
        }
    }

    /**
     * Configura listeners para eventos de otros controladores
     */
    setupEventListeners() {
        // Escuchar eventos del modal controller
        this.element.addEventListener('modal:success', (event) => {
            this.debug('Recibido evento de éxito del modal:', event.detail);
            this.loadData(); // Recargar datos después de crear/editar
        });
    }

    /**
     * Muestra mensaje de éxito
     */
    showSuccess(message) {
        this.dispatch('notification', { 
            detail: { 
                type: 'success', 
                message: message 
            }
        });
    }

    /**
     * Muestra mensaje de error
     */
    showError(message) {
        this.dispatch('notification', { 
            detail: { 
                type: 'error', 
                message: message 
            }
        });
    }

    /**
     * Logging para debugging
     */
    debug(...args) {
        if (window.MantenedorConfig?.debug) {
            console.log('[List Controller]', ...args);
        }
    }

    /**
     * Cleanup al desconectar
     */
    disconnect() {
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        this.debug('List controller desconectado');
    }
}