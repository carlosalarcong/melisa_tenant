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
            this.markFieldInvalid(this.nombrePaisTarget, this.t('mantenedores.pais.validation.nombre_required'));
            return false;
        }
        
        if (value.length < 2) {
            this.markFieldInvalid(this.nombrePaisTarget, this.t('mantenedores.pais.validation.nombre_min_length'));
            return false;
        }
        
        if (value.length > 100) {
            this.markFieldInvalid(this.nombrePaisTarget, this.t('mantenedores.pais.validation.nombre_max_length'));
            return false;
        }
        
        // Validar caracteres permitidos (letras, espacios, acentos, guiones)
        const regex = /^[a-zA-ZÀ-ÿ\u00f1\u00d1\s\-\.]+$/;
        if (!regex.test(value)) {
            this.markFieldInvalid(this.nombrePaisTarget, this.t('mantenedores.pais.validation.nombre_invalid_chars'));
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
            this.markFieldInvalid(this.nombreGentilicioTarget, this.t('mantenedores.pais.validation.gentilicio_required'));
            return false;
        }
        
        if (value.length < 2) {
            this.markFieldInvalid(this.nombreGentilicioTarget, this.t('mantenedores.pais.validation.gentilicio_min_length'));
            return false;
        }
        
        if (value.length > 100) {
            this.markFieldInvalid(this.nombreGentilicioTarget, this.t('mantenedores.pais.validation.gentilicio_max_length'));
            return false;
        }
        
        // Validar caracteres permitidos (letras, espacios, acentos)
        const regex = /^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$/;
        if (!regex.test(value)) {
            this.markFieldInvalid(this.nombreGentilicioTarget, this.t('mantenedores.pais.validation.gentilicio_invalid_chars'));
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
    
    // ==========================================
    // TRADUCCIONES ESPECÍFICAS
    // ==========================================
    
    /**
     * Hereda el sistema de traducciones del controlador base
     * y agrega traducciones específicas de países si es necesario
     */
    t(key, params = {}) {
        // Traducciones específicas adicionales para países
        const specificTranslations = {
            // Aquí se pueden agregar traducciones específicas del mantenedor de países
            // que no estén en el controlador base
        };
        
        // Buscar primero en traducciones específicas, luego en el base
        return specificTranslations[key] || super.t(key, params);
    }
}