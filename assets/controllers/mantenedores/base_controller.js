/**
 * CONTROLADOR BASE PARA MANTENEDORES - STIMULUS
 * ============================================
 * 
 * Controlador base que proporciona funcionalidad común para todos los mantenedores:
 * - Manejo de modales (crear/editar)
 * - Validación de formularios
 * - Comunicación AJAX
 * - Confirmaciones de eliminación
 * - Manejo de errores
 * - Recarga de contenido
 * 
 * PARA CREAR NUEVOS MANTENEDORES:
 * 1. Extender esta clase base
 * 2. Definir la configuración específica
 * 3. Sobrescribir métodos si es necesario
 * 
 * @author Equipo Melisa - Frontend
 * @version 1.0
 * @since 2025-10-20
 */

import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    // ==========================================
    // CONFIGURACIÓN STIMULUS
    // ==========================================
    static targets = [
        "modal",
        "form", 
        "idField",
        "title",
        "submitButton"
    ]
    
    static values = {
        entityName: String,        // Nombre de la entidad (ej: "País")
        entityNamePlural: String,  // Nombre plural (ej: "Países")
        apiBase: String,          // URL base del API (ej: "/mantenedores/basico/pais")
        modalId: String           // ID del modal (ej: "paisModal")
    }
    
    // ==========================================
    // CICLO DE VIDA DEL CONTROLADOR
    // ==========================================
    
    connect() {
        console.log(`🎯 Controlador ${this.constructor.name} conectado`);
        this.initializeController();
    }
    
    disconnect() {
        console.log(`🎯 Controlador ${this.constructor.name} desconectado`);
        this.cleanupController();
    }
    
    // ==========================================
    // INICIALIZACIÓN
    // ==========================================
    
    /**
     * Inicializa el controlador base
     */
    initializeController() {
        // Configurar modal si existe
        if (this.hasModalTarget) {
            this.setupModal();
        }
        
        // Configurar formulario si existe
        if (this.hasFormTarget) {
            this.setupForm();
        }
        
        // Configuración específica del mantenedor
        this.initializeMantenedor();
        
        console.log(`✅ Controlador inicializado:`, {
            entityName: this.entityNameValue,
            apiBase: this.apiBaseValue
        });
    }
    
    /**
     * Método para sobrescribir en clases hijas
     */
    initializeMantenedor() {
        // Implementar en clases hijas si es necesario
    }
    
    /**
     * Limpieza al desconectar
     */
    cleanupController() {
        // Limpiar event listeners si es necesario
    }
    
    // ==========================================
    // CONFIGURACIÓN DE MODAL
    // ==========================================
    
    /**
     * Configura el modal principal
     */
    setupModal() {
        if (!this.hasModalTarget) return;
        
        // Event listener para cuando se muestra el modal
        this.modalTarget.addEventListener('show.bs.modal', (event) => {
            this.handleModalShow(event);
        });
        
        // Event listener para cuando se oculta el modal
        this.modalTarget.addEventListener('hidden.bs.modal', (event) => {
            this.handleModalHidden(event);
        });
    }
    
    /**
     * Maneja la apertura del modal
     */
    handleModalShow(event) {
        const button = event.relatedTarget;
        if (!button) return;
        
        const action = button.getAttribute('data-action');
        const entityId = button.getAttribute('data-id');
        
        console.log(`📝 Configurando modal para acción: ${action}`, { entityId });
        
        // Resetear formulario
        this.resetForm();
        
        if (action === 'create') {
            this.configureCreateModal();
        } else if (action === 'edit' && entityId) {
            this.configureEditModal(entityId);
        }
    }
    
    /**
     * Maneja el cierre del modal
     */
    handleModalHidden(event) {
        this.resetForm();
    }
    
    /**
     * Configura el modal para crear
     */
    configureCreateModal() {
        if (this.hasTitleTarget) {
            this.titleTarget.innerHTML = `<i class="fas fa-plus me-2"></i>Nuevo ${this.entityNameValue}`;
        }
        
        if (this.hasIdFieldTarget) {
            this.idFieldTarget.value = '';
        }
        
        // Configuración específica para crear
        this.configureCreateDefaults();
    }
    
    /**
     * Configura el modal para editar
     */
    configureEditModal(entityId) {
        if (this.hasTitleTarget) {
            this.titleTarget.innerHTML = `<i class="fas fa-edit me-2"></i>Editar ${this.entityNameValue}`;
        }
        
        if (this.hasIdFieldTarget) {
            this.idFieldTarget.value = entityId;
        }
        
        // Cargar datos de la entidad
        this.loadEntityData(entityId);
    }
    
    /**
     * Método para sobrescribir - valores por defecto al crear
     */
    configureCreateDefaults() {
        // Implementar en clases hijas
    }
    
    // ==========================================
    // CONFIGURACIÓN DE FORMULARIO
    // ==========================================
    
    /**
     * Configura el formulario
     */
    setupForm() {
        if (!this.hasFormTarget) return;
        
        this.formTarget.addEventListener('submit', (event) => {
            this.handleFormSubmit(event);
        });
    }
    
    /**
     * Maneja el envío del formulario
     */
    handleFormSubmit(event) {
        event.preventDefault();
        
        // Validar formulario
        if (!this.validateForm()) {
            console.log('❌ Formulario no válido');
            return;
        }
        
        const entityId = this.hasIdFieldTarget ? this.idFieldTarget.value : '';
        const isEdit = entityId && entityId !== '';
        
        console.log(`💾 Enviando formulario...`, { isEdit, entityId });
        
        // Deshabilitar botón de envío
        this.setSubmitButtonLoading(true);
        
        // Enviar datos
        if (isEdit) {
            this.updateEntity(entityId);
        } else {
            this.createEntity();
        }
    }
    
    /**
     * Resetea el formulario
     */
    resetForm() {
        if (!this.hasFormTarget) return;
        
        this.formTarget.reset();
        this.formTarget.classList.remove('was-validated');
        
        // Limpiar clases de validación
        const fields = this.formTarget.querySelectorAll('.form-control');
        fields.forEach(field => {
            field.classList.remove('is-valid', 'is-invalid');
        });
        
        console.log('🧹 Formulario reseteado');
    }
    
    // ==========================================
    // VALIDACIÓN
    // ==========================================
    
    /**
     * Valida el formulario - método base
     * Debe ser sobrescrito en clases hijas
     */
    validateForm() {
        if (!this.hasFormTarget) return false;
        
        // Activar validación visual de Bootstrap
        this.formTarget.classList.add('was-validated');
        
        // Validación específica del mantenedor
        return this.validateSpecificFields();
    }
    
    /**
     * Método para sobrescribir - validación específica
     */
    validateSpecificFields() {
        // Implementar en clases hijas
        return true;
    }
    
    /**
     * Marca un campo como inválido
     */
    markFieldInvalid(field, message) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
        
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = message;
        }
    }
    
    /**
     * Marca un campo como válido
     */
    markFieldValid(field) {
        field.classList.add('is-valid');
        field.classList.remove('is-invalid');
    }
    
    // ==========================================
    // OPERACIONES CRUD
    // ==========================================
    
    /**
     * Carga datos de una entidad
     */
    async loadEntityData(entityId) {
        if (!this.hasTitleTarget) return;
        
        // Mostrar indicador de carga
        const originalTitle = this.titleTarget.innerHTML;
        this.titleTarget.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Cargando datos...';
        
        try {
            const response = await fetch(`${this.apiBaseValue}/${entityId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success && data.data) {
                // Poblar formulario con datos
                this.populateForm(data.data);
                
                // Restaurar título
                this.titleTarget.innerHTML = `<i class="fas fa-edit me-2"></i>Editar ${this.entityNameValue}`;
            } else {
                this.titleTarget.innerHTML = originalTitle;
                this.showError(data.error || 'No se pudieron cargar los datos');
            }
        } catch (error) {
            console.error('Error de conexión:', error);
            this.titleTarget.innerHTML = originalTitle;
            this.showConnectionError(error.message);
        }
    }
    
    /**
     * Crea una nueva entidad
     */
    async createEntity() {
        const data = this.collectFormData();
        
        try {
            const response = await fetch(this.apiBaseValue, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                this.showSuccess(result.message || `${this.entityNameValue} creado exitosamente`);
                this.closeModal();
                this.reloadContent();
            } else {
                this.showFormError(result.error);
            }
        } catch (error) {
            console.error('Error de conexión:', error);
            this.showConnectionError();
        } finally {
            this.setSubmitButtonLoading(false);
        }
    }
    
    /**
     * Actualiza una entidad existente
     */
    async updateEntity(entityId) {
        const data = this.collectFormData();
        
        console.log(`📤 Enviando datos para actualizar ${entityId}:`, data);
        
        try {
            const response = await fetch(`${this.apiBaseValue}/${entityId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            console.log('📡 Respuesta del servidor (actualizar):', result);
            
            if (result.success) {
                console.log('✅ Entidad actualizada exitosamente');
                
                this.showSuccess(result.message || `${this.entityNameValue} actualizado exitosamente`);
                this.closeModal();
                this.reloadContent();
            } else {
                console.error('❌ Error al actualizar:', result.error);
                this.showFormError(result.error);
            }
        } catch (error) {
            console.error('❌ Error de conexión:', error);
            this.showConnectionError();
        } finally {
            this.setSubmitButtonLoading(false);
        }
    }
    
    // ==========================================
    // MANEJO DE ELIMINACIÓN
    // ==========================================
    
    /**
     * Maneja click en botón eliminar
     */
    handleDelete(event) {
        event.preventDefault();
        
        const button = event.currentTarget;
        const entityId = button.getAttribute('data-id');
        const entityName = button.getAttribute('data-name');
        
        console.log(`🗑️ Solicitud de eliminación`, { entityId, entityName });
        
        this.confirmDelete(entityId, entityName);
    }
    
    /**
     * Confirma eliminación
     */
    confirmDelete(entityId, entityName) {
        Swal.fire({
            title: '¿Está seguro?',
            text: `¿Realmente desea eliminar ${this.entityNameValue.toLowerCase()} "${entityName}"? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.deleteEntity(entityId);
            }
        });
    }
    
    /**
     * Elimina una entidad
     */
    async deleteEntity(entityId) {
        console.log(`🗑️ Eliminando entidad ID: ${entityId}`);
        
        try {
            const response = await fetch(`${this.apiBaseValue}/${entityId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            console.log('📡 Respuesta del servidor (eliminar):', result);
            
            if (result.success) {
                console.log('✅ Entidad eliminada exitosamente');
                
                this.showSuccess(result.message || `${this.entityNameValue} eliminado exitosamente`);
                this.reloadContent();
            } else {
                console.error('❌ Error al eliminar:', result.error);
                this.showError(result.error || `No se pudo eliminar ${this.entityNameValue.toLowerCase()}`);
            }
        } catch (error) {
            console.error('❌ Error de conexión:', error);
            this.showConnectionError();
        }
    }
    
    // ==========================================
    // MÉTODOS AUXILIARES
    // ==========================================
    
    /**
     * Pobla el formulario con datos - debe ser sobrescrito
     */
    populateForm(data) {
        // Implementar en clases hijas
        console.warn('populateForm debe ser implementado en la clase hija');
    }
    
    /**
     * Recopila datos del formulario - debe ser sobrescrito
     */
    collectFormData() {
        // Implementar en clases hijas
        console.warn('collectFormData debe ser implementado en la clase hija');
        return {};
    }
    
    /**
     * Cierra el modal
     */
    closeModal() {
        if (!this.hasModalTarget) return;
        
        const modalInstance = bootstrap.Modal.getInstance(this.modalTarget);
        if (modalInstance) {
            modalInstance.hide();
        }
    }
    
    /**
     * Recarga el contenido del mantenedor
     */
    reloadContent() {
        console.log('🔄 Recargando contenido del mantenedor...');
        
        // Si existe la función global cargarMantenedor, la usamos
        if (typeof cargarMantenedor === 'function') {
            cargarMantenedor(this.getMantenedorName());
        } else {
            // Fallback: recargar página
            window.location.reload();
        }
    }
    
    /**
     * Obtiene el nombre del mantenedor - debe ser sobrescrito
     */
    getMantenedorName() {
        // Implementar en clases hijas
        return 'default';
    }
    
    /**
     * Controla el estado de carga del botón de envío
     */
    setSubmitButtonLoading(loading) {
        if (!this.hasSubmitButtonTarget) return;
        
        if (loading) {
            this.submitButtonTarget.disabled = true;
            this.submitButtonTarget.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...';
        } else {
            this.submitButtonTarget.disabled = false;
            this.submitButtonTarget.innerHTML = `<i class="fas fa-save me-1"></i>Guardar ${this.entityNameValue}`;
        }
    }
    
    // ==========================================
    // NOTIFICACIONES
    // ==========================================
    
    /**
     * Muestra mensaje de éxito
     */
    showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: message,
            timer: 2000,
            showConfirmButton: false
        });
    }
    
    /**
     * Muestra error de formulario
     */
    showFormError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error en el formulario',
            text: message || 'Por favor, revise los datos ingresados'
        });
    }
    
    /**
     * Muestra error general
     */
    showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    }
    
    /**
     * Muestra error de conexión
     */
    showConnectionError(details = null) {
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: details ? `No se pudo conectar con el servidor: ${details}` : 'No se pudo conectar con el servidor. Intente nuevamente.'
        });
    }
}