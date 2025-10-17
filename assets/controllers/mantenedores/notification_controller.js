import { Controller } from "@hotwired/stimulus";

/**
 * Controlador para notificaciones toast del sistema de mantenedores
 * Maneja la creación, visualización y ocultación de notificaciones
 */
export default class extends Controller {
    static targets = ["container"];
    
    static values = {
        duration: { type: Number, default: 5000 }, // Duración por defecto en ms
        position: { type: String, default: 'top-right' }
    };

    connect() {
        this.debug('Notification controller conectado');
        this.toastCounter = 0;
        this.activeToasts = new Map();
        
        // Escuchar eventos de notificación de otros controladores
        this.setupEventListeners();
        
        // Configurar posición del contenedor si es necesario
        this.setupContainer();
    }

    /**
     * Muestra una notificación de éxito
     */
    showSuccess(message, options = {}) {
        this.show(message, 'success', options);
    }

    /**
     * Muestra una notificación de error
     */
    showError(message, options = {}) {
        this.show(message, 'error', options);
    }

    /**
     * Muestra una notificación de advertencia
     */
    showWarning(message, options = {}) {
        this.show(message, 'warning', options);
    }

    /**
     * Muestra una notificación de información
     */
    showInfo(message, options = {}) {
        this.show(message, 'info', options);
    }

    /**
     * Muestra una notificación personalizada
     */
    show(message, type = 'info', options = {}) {
        const config = {
            id: `toast-${++this.toastCounter}`,
            message: message,
            type: type,
            duration: options.duration || this.durationValue,
            autoHide: options.autoHide !== false,
            showProgress: options.showProgress !== false,
            actions: options.actions || [],
            ...options
        };

        this.debug('Mostrando notificación:', config);

        const toastElement = this.createToastElement(config);
        this.containerTarget.appendChild(toastElement);

        // Activar el toast con Bootstrap
        const toast = new bootstrap.Toast(toastElement, {
            autohide: config.autoHide,
            delay: config.duration
        });

        // Guardar referencia
        this.activeToasts.set(config.id, {
            element: toastElement,
            toast: toast,
            config: config
        });

        // Mostrar el toast
        toast.show();

        // Configurar progreso si está habilitado
        if (config.showProgress && config.autoHide) {
            this.startProgressBar(toastElement, config.duration);
        }

        // Limpiar cuando se oculte
        toastElement.addEventListener('hidden.bs.toast', () => {
            this.removeToast(config.id);
        });

        return config.id;
    }

    /**
     * Crea el elemento DOM del toast
     */
    createToastElement(config) {
        const toastElement = document.createElement('div');
        toastElement.className = `toast notification-toast toast-${config.type}`;
        toastElement.id = config.id;
        toastElement.setAttribute('role', 'alert');
        toastElement.setAttribute('aria-live', 'assertive');
        toastElement.setAttribute('aria-atomic', 'true');

        const iconClass = this.getIconClass(config.type);
        const actionsHtml = this.renderActions(config.actions, config.id);
        const progressHtml = config.showProgress && config.autoHide ? 
            '<div class="toast-progress"><div class="toast-progress-bar"></div></div>' : '';

        toastElement.innerHTML = `
            <div class="toast-header">
                <i class="${iconClass} me-2"></i>
                <strong class="me-auto">${this.getTypeTitle(config.type)}</strong>
                <small class="text-muted">${this.getTimeStamp()}</small>
                <button type="button" 
                        class="btn-close" 
                        data-bs-dismiss="toast" 
                        aria-label="Cerrar"></button>
            </div>
            <div class="toast-body">
                ${config.message}
                ${actionsHtml}
            </div>
            ${progressHtml}
        `;

        // Agregar animación de entrada
        toastElement.classList.add('slide-up');

        return toastElement;
    }

    /**
     * Renderiza acciones del toast
     */
    renderActions(actions, toastId) {
        if (!actions || actions.length === 0) return '';

        const actionsHtml = actions.map(action => `
            <button type="button" 
                    class="btn btn-sm btn-outline-${action.style || 'primary'} me-2"
                    data-action="click->notification#executeAction"
                    data-toast-id="${toastId}"
                    data-action-type="${action.type}">
                ${action.label}
            </button>
        `).join('');

        return `<div class="toast-actions mt-2">${actionsHtml}</div>`;
    }

    /**
     * Ejecuta una acción de toast
     */
    executeAction(event) {
        const button = event.currentTarget;
        const toastId = button.dataset.toastId;
        const actionType = button.dataset.actionType;

        this.debug('Ejecutando acción:', actionType, 'en toast:', toastId);

        // Disparar evento personalizado
        this.dispatch('action', {
            detail: {
                toastId: toastId,
                actionType: actionType,
                button: button
            }
        });

        // Cerrar el toast después de la acción
        this.hideToast(toastId);
    }

    /**
     * Inicia la barra de progreso
     */
    startProgressBar(toastElement, duration) {
        const progressBar = toastElement.querySelector('.toast-progress-bar');
        if (!progressBar) return;

        progressBar.style.transition = `width ${duration}ms linear`;
        progressBar.style.width = '0%';

        // Iniciar animación
        setTimeout(() => {
            progressBar.style.width = '100%';
        }, 50);
    }

    /**
     * Oculta un toast específico
     */
    hideToast(toastId) {
        const toastData = this.activeToasts.get(toastId);
        if (toastData) {
            toastData.toast.hide();
        }
    }

    /**
     * Remueve un toast del DOM y del tracking
     */
    removeToast(toastId) {
        const toastData = this.activeToasts.get(toastId);
        if (toastData) {
            toastData.element.remove();
            this.activeToasts.delete(toastId);
            this.debug('Toast removido:', toastId);
        }
    }

    /**
     * Limpia todas las notificaciones
     */
    clearAll() {
        this.debug('Limpiando todas las notificaciones');
        
        this.activeToasts.forEach((toastData, toastId) => {
            toastData.toast.hide();
        });
    }

    /**
     * Obtiene la clase de icono según el tipo
     */
    getIconClass(type) {
        const icons = {
            success: 'fas fa-check-circle text-success',
            error: 'fas fa-exclamation-circle text-danger',
            warning: 'fas fa-exclamation-triangle text-warning',
            info: 'fas fa-info-circle text-info'
        };
        
        return icons[type] || icons.info;
    }

    /**
     * Obtiene el título según el tipo
     */
    getTypeTitle(type) {
        const titles = {
            success: 'Éxito',
            error: 'Error',
            warning: 'Advertencia',
            info: 'Información'
        };
        
        return titles[type] || titles.info;
    }

    /**
     * Obtiene timestamp formateado
     */
    getTimeStamp() {
        const now = new Date();
        return now.toLocaleTimeString();
    }

    /**
     * Configura el contenedor de notificaciones
     */
    setupContainer() {
        const position = this.positionValue;
        
        // Agregar clases CSS según la posición
        this.containerTarget.classList.add(`toast-position-${position}`);
        
        // Configurar estilos inline si es necesario
        if (position.includes('top')) {
            this.containerTarget.style.top = '20px';
        }
        if (position.includes('bottom')) {
            this.containerTarget.style.bottom = '20px';
        }
        if (position.includes('right')) {
            this.containerTarget.style.right = '20px';
        }
        if (position.includes('left')) {
            this.containerTarget.style.left = '20px';
        }
    }

    /**
     * Configura listeners para eventos de otros controladores
     */
    setupEventListeners() {
        // Escuchar eventos de notificación
        this.element.addEventListener('notification', (event) => {
            const { type, message, options } = event.detail;
            this.show(message, type, options || {});
        });

        // Escuchar eventos específicos del sistema
        this.element.addEventListener('entity:created', (event) => {
            this.showSuccess('Registro creado exitosamente');
        });

        this.element.addEventListener('entity:updated', (event) => {
            this.showSuccess('Registro actualizado exitosamente');
        });

        this.element.addEventListener('entity:deleted', (event) => {
            this.showSuccess('Registro eliminado exitosamente');
        });

        this.element.addEventListener('error:validation', (event) => {
            this.showError('Por favor, corrige los errores en el formulario');
        });

        this.element.addEventListener('error:network', (event) => {
            this.showError('Error de conexión. Por favor, intenta nuevamente.');
        });
    }

    /**
     * Maneja notificaciones desde eventos Stimulus
     */
    notificationReceived(event) {
        const { type, message, options } = event.detail;
        this.show(message, type, options);
    }

    /**
     * Crea notificaciones con acciones personalizadas
     */
    showWithActions(message, type, actions, options = {}) {
        return this.show(message, type, {
            ...options,
            actions: actions,
            autoHide: false // No auto-ocultar cuando hay acciones
        });
    }

    /**
     * Muestra notificación de confirmación
     */
    showConfirmation(message, onConfirm, onCancel = null) {
        const actions = [
            {
                label: 'Confirmar',
                type: 'confirm',
                style: 'success'
            },
            {
                label: 'Cancelar', 
                type: 'cancel',
                style: 'secondary'
            }
        ];

        const toastId = this.showWithActions(message, 'warning', actions);

        // Escuchar acciones
        const actionHandler = (event) => {
            if (event.detail.toastId === toastId) {
                if (event.detail.actionType === 'confirm' && onConfirm) {
                    onConfirm();
                } else if (event.detail.actionType === 'cancel' && onCancel) {
                    onCancel();
                }
                
                // Remover listener
                this.element.removeEventListener('notification:action', actionHandler);
            }
        };

        this.element.addEventListener('notification:action', actionHandler);

        return toastId;
    }

    /**
     * Muestra notificación de carga
     */
    showLoading(message = 'Cargando...') {
        return this.show(`
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-2" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                ${message}
            </div>
        `, 'info', {
            autoHide: false,
            showProgress: false
        });
    }

    /**
     * Oculta notificación de carga
     */
    hideLoading(loadingToastId) {
        if (loadingToastId) {
            this.hideToast(loadingToastId);
        }
    }

    /**
     * Obtiene estadísticas de notificaciones activas
     */
    getStats() {
        const stats = {
            total: this.activeToasts.size,
            byType: {}
        };

        this.activeToasts.forEach(toastData => {
            const type = toastData.config.type;
            stats.byType[type] = (stats.byType[type] || 0) + 1;
        });

        return stats;
    }

    /**
     * Logging para debugging
     */
    debug(...args) {
        if (window.MantenedorConfig?.debug) {
            console.log('[Notification Controller]', ...args);
        }
    }

    /**
     * Cleanup al desconectar
     */
    disconnect() {
        this.clearAll();
        this.debug('Notification controller desconectado');
    }
}