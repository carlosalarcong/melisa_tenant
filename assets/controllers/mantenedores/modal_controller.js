import { Controller } from "@hotwired/stimulus";

/**
 * Controlador para modales de creación/edición de mantenedores
 * Maneja apertura, cierre, validación y envío de formularios
 */
export default class extends Controller {
    static targets = [
        "modal", "form", "title", "submitButton", "submitSpinner", 
        "entityId", "closeButton"
    ];
    
    static values = {
        createUrl: String,
        updateUrl: String,
        csrfToken: String
    };

    connect() {
        this.debug('Modal controller conectado');
        this.isSubmitting = false;
        this.currentMode = 'create'; // 'create' or 'edit'
        this.setupFormValidation();
        this.setupKeyboardHandlers();
    }

    /**
     * Abre el modal en modo creación
     */
    openCreate(event) {
        event?.preventDefault();
        
        this.debug('Abriendo modal en modo creación');
        this.currentMode = 'create';
        this.resetForm();
        this.updateModalTitle('Crear');
        this.showModal();
    }

    /**
     * Abre el modal en modo edición
     */
    openEdit(event) {
        event?.preventDefault();
        
        const entityId = event.currentTarget.dataset.entityId;
        if (!entityId) {
            this.showError('ID de entidad no proporcionado');
            return;
        }

        this.debug('Abriendo modal en modo edición para ID:', entityId);
        this.currentMode = 'edit';
        this.entityIdTarget.value = entityId;
        this.updateModalTitle('Editar');
        
        // Cargar datos de la entidad
        this.loadEntityData(entityId);
        this.showModal();
    }

    /**
     * Cierra el modal
     */
    close(event) {
        event?.preventDefault();
        
        if (this.isSubmitting) {
            this.debug('No se puede cerrar el modal mientras se está enviando');
            return;
        }

        this.debug('Cerrando modal');
        this.hideModal();
        this.resetForm();
    }

    /**
     * Envía el formulario
     */
    async submitForm(event) {
        event.preventDefault();
        
        if (this.isSubmitting) {
            this.debug('Ya se está enviando el formulario');
            return;
        }

        this.debug('Enviando formulario en modo:', this.currentMode);
        
        try {
            this.startSubmission();
            
            // Validar formulario antes de enviar
            if (!this.validateForm()) {
                this.stopSubmission();
                return;
            }

            const formData = new FormData(this.formTarget);
            formData.append('_token', this.csrfTokenValue);
            
            if (this.currentMode === 'edit') {
                formData.append('_method', 'PUT');
            }

            const url = this.getSubmissionUrl();
            const response = await this.submitRequest(url, formData);
            
            await this.handleSubmissionResponse(response);
            
        } catch (error) {
            this.debug('Error en envío:', error);
            this.handleSubmissionError(error);
        } finally {
            this.stopSubmission();
        }
    }

    /**
     * Carga los datos de una entidad para edición
     */
    async loadEntityData(entityId) {
        try {
            this.showLoadingInModal();
            
            const showUrl = this.getShowUrl(entityId);
            const response = await fetch(showUrl, {
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
                this.populateForm(data.data);
            } else {
                throw new Error(data.message || 'Error al cargar datos');
            }
            
        } catch (error) {
            this.debug('Error cargando datos:', error);
            this.showError('Error al cargar los datos de la entidad');
        } finally {
            this.hideLoadingInModal();
        }
    }

    /**
     * Llena el formulario con datos de la entidad
     */
    populateForm(entityData) {
        this.debug('Llenando formulario con datos:', entityData);
        
        Object.keys(entityData).forEach(fieldName => {
            const field = this.formTarget.querySelector(`[name="${fieldName}"]`);
            
            if (field) {
                if (field.type === 'checkbox') {
                    field.checked = Boolean(entityData[fieldName]);
                } else {
                    field.value = entityData[fieldName] || '';
                }
                
                // Limpiar errores previos
                this.clearFieldError(field);
            }
        });
    }

    /**
     * Realiza la petición de envío
     */
    async submitRequest(url, formData) {
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        return response;
    }

    /**
     * Maneja la respuesta del envío
     */
    async handleSubmissionResponse(response) {
        const data = await response.json();
        
        if (response.ok && data.success) {
            this.debug('Envío exitoso:', data);
            this.showSuccess(data.message || 'Operación realizada exitosamente');
            this.hideModal();
            this.resetForm();
            
            // Notificar a otros controladores sobre el cambio
            this.dispatch('success', { 
                detail: { 
                    action: this.currentMode,
                    data: data.data,
                    message: data.message
                }
            });
            
        } else {
            this.debug('Error en respuesta:', data);
            this.handleValidationErrors(data.errors || {});
            this.showError(data.message || 'Error en la operación');
        }
    }

    /**
     * Maneja errores de envío
     */
    handleSubmissionError(error) {
        this.debug('Error de red o servidor:', error);
        this.showError('Error de conexión. Por favor, intenta nuevamente.');
    }

    /**
     * Maneja errores de validación del servidor
     */
    handleValidationErrors(errors) {
        this.debug('Errores de validación:', errors);
        
        // Limpiar errores previos
        this.clearAllFieldErrors();
        
        // Mostrar nuevos errores
        Object.keys(errors).forEach(fieldName => {
            const field = this.formTarget.querySelector(`[name="${fieldName}"]`);
            if (field) {
                this.showFieldError(field, errors[fieldName]);
            }
        });
    }

    /**
     * Validación del lado del cliente
     */
    validateForm() {
        let isValid = true;
        this.clearAllFieldErrors();
        
        // Validar campos requeridos
        const requiredFields = this.formTarget.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                this.showFieldError(field, 'Este campo es obligatorio');
                isValid = false;
            }
        });
        
        // Validaciones personalizadas
        isValid = this.validateCustomRules() && isValid;
        
        return isValid;
    }

    /**
     * Validaciones personalizadas específicas del formulario
     */
    validateCustomRules() {
        let isValid = true;
        
        // Ejemplo: validar códigos únicos
        const codigoField = this.formTarget.querySelector('[name="codigo"]');
        if (codigoField && codigoField.value) {
            // Esta validación se haría en el servidor, pero podemos hacer validaciones básicas aquí
            if (codigoField.value.length === 0) {
                this.showFieldError(codigoField, 'El código no puede estar vacío');
                isValid = false;
            }
        }
        
        return isValid;
    }

    /**
     * Muestra error en un campo específico
     */
    showFieldError(field, message) {
        field.classList.add('is-invalid');
        
        const errorElement = this.formTarget.querySelector(`[data-field="${field.name}"]`);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }

    /**
     * Limpia error de un campo específico
     */
    clearFieldError(field) {
        field.classList.remove('is-invalid');
        
        const errorElement = this.formTarget.querySelector(`[data-field="${field.name}"]`);
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }

    /**
     * Limpia todos los errores de campos
     */
    clearAllFieldErrors() {
        const fields = this.formTarget.querySelectorAll('.is-invalid');
        fields.forEach(field => {
            this.clearFieldError(field);
        });
    }

    /**
     * Inicia el estado de envío
     */
    startSubmission() {
        this.isSubmitting = true;
        this.submitButtonTarget.disabled = true;
        this.submitSpinnerTarget.classList.remove('d-none');
        
        if (this.hasCloseButtonTarget) {
            this.closeButtonTarget.disabled = true;
        }
    }

    /**
     * Detiene el estado de envío
     */
    stopSubmission() {
        this.isSubmitting = false;
        this.submitButtonTarget.disabled = false;
        this.submitSpinnerTarget.classList.add('d-none');
        
        if (this.hasCloseButtonTarget) {
            this.closeButtonTarget.disabled = false;
        }
    }

    /**
     * Muestra el modal
     */
    showModal() {
        const modalElement = document.getElementById('mantenedorModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }
    }

    /**
     * Oculta el modal
     */
    hideModal() {
        const modalElement = document.getElementById('mantenedorModal');
        if (modalElement) {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        }
    }

    /**
     * Resetea el formulario
     */
    resetForm() {
        this.formTarget.reset();
        this.entityIdTarget.value = '';
        this.clearAllFieldErrors();
        this.currentMode = 'create';
    }

    /**
     * Actualiza el título del modal
     */
    updateModalTitle(action) {
        const entityName = window.MantenedorConfig?.entity_name || 'Registro';
        this.titleTarget.textContent = `${action} ${entityName}`;
    }

    /**
     * Obtiene la URL para envío según el modo
     */
    getSubmissionUrl() {
        if (this.currentMode === 'edit') {
            return this.updateUrlValue.replace('ID_PLACEHOLDER', this.entityIdTarget.value);
        }
        return this.createUrlValue;
    }

    /**
     * Obtiene la URL para mostrar una entidad
     */
    getShowUrl(entityId) {
        const showUrl = window.MantenedorConfig?.routes?.show_url || '';
        return showUrl.replace('ID_PLACEHOLDER', entityId);
    }

    /**
     * Muestra loading en el modal
     */
    showLoadingInModal() {
        // Implementar si es necesario
    }

    /**
     * Oculta loading en el modal
     */
    hideLoadingInModal() {
        // Implementar si es necesario
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
     * Configura validación en tiempo real
     */
    setupFormValidation() {
        // Validación en tiempo real mientras el usuario escribe
        const fields = this.formTarget.querySelectorAll('input, select, textarea');
        fields.forEach(field => {
            field.addEventListener('blur', () => {
                this.validateSingleField(field);
            });
            
            field.addEventListener('input', () => {
                // Limpiar error si el campo ya no está vacío
                if (field.value.trim() && field.classList.contains('is-invalid')) {
                    this.clearFieldError(field);
                }
            });
        });
    }

    /**
     * Valida un campo individual
     */
    validateSingleField(field) {
        if (field.hasAttribute('required') && !field.value.trim()) {
            this.showFieldError(field, 'Este campo es obligatorio');
            return false;
        }
        
        this.clearFieldError(field);
        return true;
    }

    /**
     * Configura manejadores de teclado
     */
    setupKeyboardHandlers() {
        document.addEventListener('keydown', (event) => {
            // Cerrar modal con Escape
            if (event.key === 'Escape' && !this.isSubmitting) {
                this.close();
            }
            
            // Enviar formulario con Ctrl+Enter
            if (event.ctrlKey && event.key === 'Enter' && !this.isSubmitting) {
                this.submitForm(event);
            }
        });
    }

    /**
     * Logging para debugging
     */
    debug(...args) {
        if (window.MantenedorConfig?.debug) {
            console.log('[Modal Controller]', ...args);
        }
    }

    /**
     * Cleanup al desconectar
     */
    disconnect() {
        this.debug('Modal controller desconectado');
    }
}