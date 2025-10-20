/**
 * CONTROLADOR ESPECÍFICO PARA MANTENEDOR DE PAÍSES - STIMULUS
 * =========================================================
 * 
 * Controlador específico que extiende BaseController para el mantenedor de países.
 * Contiene la lógica específica para la gestión de países:
 * - Validación de campos específicos (nombre, gentilicio)
 * - Configuración de valores por defecto
 * - Poblado de formulario con datos de país
 * - Recopilación de datos específicos del formulario
 * 
 * @author Equipo Melisa - Frontend
 * @version 1.0
 * @since 2025-10-20
 */

import BaseController from "../base_controller.js"

export default class extends BaseController {
    // ==========================================
    // CONFIGURACIÓN STIMULUS
    // ==========================================
    static targets = [
        ...BaseController.targets,
        "nombrePais",
        "nombreGentilicio", 
        "activo"
    ]
    
    static values = {
        ...BaseController.values
    }
    
    // ==========================================
    // INICIALIZACIÓN ESPECÍFICA
    // ==========================================
    
    /**
     * Inicialización específica del mantenedor de países
     */
    initializeMantenedor() {
        console.log('🏳️ Inicializando controlador de países');
        
        // Configurar validación en tiempo real si se desea
        this.setupRealTimeValidation();
    }
    
    /**
     * Configura validación en tiempo real
     */
    setupRealTimeValidation() {
        if (this.hasNombrePaisTarget) {
            this.nombrePaisTarget.addEventListener('input', () => {
                this.validateNombrePais();
            });
        }
        
        if (this.hasNombreGentilicioTarget) {
            this.nombreGentilicioTarget.addEventListener('input', () => {
                this.validateNombreGentilicio();
            });
        }
    }
    
    // ==========================================
    // CONFIGURACIÓN ESPECÍFICA
    // ==========================================
    
    /**
     * Valores por defecto al crear nuevo país
     */
    configureCreateDefaults() {
        if (this.hasActivoTarget) {
            this.activoTarget.checked = true;
        }
    }
    
    /**
     * Nombre del mantenedor para recarga de contenido
     */
    getMantenedorName() {
        return 'pais';
    }
    
    // ==========================================
    // VALIDACIÓN ESPECÍFICA
    // ==========================================
    
    /**
     * Validación específica de campos de país
     */
    validateSpecificFields() {
        let isValid = true;
        
        // Validar nombre del país
        if (!this.validateNombrePais()) {
            isValid = false;
        }
        
        // Validar gentilicio
        if (!this.validateNombreGentilicio()) {
            isValid = false;
        }
        
        return isValid;
    }
    
    /**
     * Valida el campo nombre del país
     */
    validateNombrePais() {
        if (!this.hasNombrePaisTarget) return true;
        
        const value = this.nombrePaisTarget.value.trim();
        
        if (!value) {
            this.markFieldInvalid(this.nombrePaisTarget, 'El nombre del país es obligatorio');
            return false;
        }
        
        if (value.length < 2) {
            this.markFieldInvalid(this.nombrePaisTarget, 'El nombre debe tener al menos 2 caracteres');
            return false;
        }
        
        if (value.length > 100) {
            this.markFieldInvalid(this.nombrePaisTarget, 'El nombre no puede exceder 100 caracteres');
            return false;
        }
        
        // Validar caracteres permitidos (letras, espacios, acentos, guiones)
        const regex = /^[a-zA-ZÀ-ÿ\u00f1\u00d1\s\-\.]+$/;
        if (!regex.test(value)) {
            this.markFieldInvalid(this.nombrePaisTarget, 'El nombre solo puede contener letras, espacios y guiones');
            return false;
        }
        
        this.markFieldValid(this.nombrePaisTarget);
        return true;
    }
    
    /**
     * Valida el campo gentilicio
     */
    validateNombreGentilicio() {
        if (!this.hasNombreGentilicioTarget) return true;
        
        const value = this.nombreGentilicioTarget.value.trim();
        
        if (!value) {
            this.markFieldInvalid(this.nombreGentilicioTarget, 'El gentilicio es obligatorio');
            return false;
        }
        
        if (value.length < 2) {
            this.markFieldInvalid(this.nombreGentilicioTarget, 'El gentilicio debe tener al menos 2 caracteres');
            return false;
        }
        
        if (value.length > 100) {
            this.markFieldInvalid(this.nombreGentilicioTarget, 'El gentilicio no puede exceder 100 caracteres');
            return false;
        }
        
        // Validar caracteres permitidos (letras, espacios, acentos)
        const regex = /^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$/;
        if (!regex.test(value)) {
            this.markFieldInvalid(this.nombreGentilicioTarget, 'El gentilicio solo puede contener letras y espacios');
            return false;
        }
        
        this.markFieldValid(this.nombreGentilicioTarget);
        return true;
    }
    
    // ==========================================
    // MANEJO DE DATOS
    // ==========================================
    
    /**
     * Pobla el formulario con datos de país
     */
    populateForm(paisData) {
        console.log('📝 Poblando formulario con datos:', paisData);
        
        if (this.hasNombrePaisTarget) {
            this.nombrePaisTarget.value = paisData.nombrePais || '';
        }
        
        if (this.hasNombreGentilicioTarget) {
            this.nombreGentilicioTarget.value = paisData.nombreGentilicio || '';
        }
        
        if (this.hasActivoTarget) {
            this.activoTarget.checked = paisData.activo || false;
        }
    }
    
    /**
     * Recopila datos específicos del formulario de países
     */
    collectFormData() {
        const data = {
            nombrePais: this.hasNombrePaisTarget ? this.nombrePaisTarget.value.trim() : '',
            nombreGentilicio: this.hasNombreGentilicioTarget ? this.nombreGentilicioTarget.value.trim() : '',
            activo: this.hasActivoTarget ? this.activoTarget.checked : false
        };
        
        console.log('📦 Datos recopilados del formulario:', data);
        return data;
    }
    
    // ==========================================
    // ACCIONES STIMULUS
    // ==========================================
    
    /**
     * Acción para limpiar formulario
     */
    clearForm() {
        this.resetForm();
        if (this.hasActivoTarget) {
            this.activoTarget.checked = true;
        }
    }
    
    /**
     * Acción para convertir texto a formato título
     */
    formatToTitle(event) {
        const field = event.target;
        const words = field.value.toLowerCase().split(' ');
        const titleCase = words.map(word => 
            word.charAt(0).toUpperCase() + word.slice(1)
        ).join(' ');
        field.value = titleCase;
    }
    
    /**
     * Acción para validar campo en tiempo real
     */
    validateField(event) {
        const field = event.target;
        
        if (field === this.nombrePaisTarget) {
            this.validateNombrePais();
        } else if (field === this.nombreGentilicioTarget) {
            this.validateNombreGentilicio();
        }
    }
    
    // ==========================================
    // UTILIDADES ESPECÍFICAS
    // ==========================================
    
    /**
     * Genera gentilicio automáticamente basado en el nombre del país
     */
    generateGentilicio() {
        if (!this.hasNombrePaisTarget || !this.hasNombreGentilicioTarget) return;
        
        const paisNombre = this.nombrePaisTarget.value.trim();
        if (!paisNombre) return;
        
        // Reglas básicas para generar gentilicios (simplificado)
        let gentilicio = paisNombre;
        
        // Casos específicos conocidos
        const gentilicios = {
            'Chile': 'Chileno',
            'Argentina': 'Argentino', 
            'Brasil': 'Brasileño',
            'Perú': 'Peruano',
            'Colombia': 'Colombiano',
            'Venezuela': 'Venezolano',
            'Ecuador': 'Ecuatoriano',
            'Uruguay': 'Uruguayo',
            'Paraguay': 'Paraguayo',
            'Bolivia': 'Boliviano'
        };
        
        if (gentilicios[paisNombre]) {
            gentilicio = gentilicios[paisNombre];
        } else {
            // Regla general: agregar terminación
            if (paisNombre.endsWith('a')) {
                gentilicio = paisNombre.slice(0, -1) + 'ano';
            } else {
                gentilicio = paisNombre + 'ano';
            }
        }
        
        this.nombreGentilicioTarget.value = gentilicio;
        this.validateNombreGentilicio();
    }
    
    /**
     * Acción para generar gentilicio automáticamente
     */
    autoGenerateGentilicio() {
        this.generateGentilicio();
    }
}