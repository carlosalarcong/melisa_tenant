import { Controller } from "@hotwired/stimulus"

/**
 * Controller interno para manejo de pacientes (sin API Platform)
 * 
 * Este controller maneja funcionalidades internas del sistema
 * sin depender de API Platform - para formularios, validaciones,
 * UI interactions, etc.
 */
export default class extends Controller {
    
    // Targets para elementos del DOM
    static targets = [
        // Información básica del paciente
        "form", "name", "cedula", "email", "phone", "address",
        "gender", "birthDate", "bloodType",
        
        // Elementos de UI
        "submitButton", "cancelButton", "loading", "error", "success",
        
        // Contenedores
        "patientInfo", "medicalInfo", "emergencyInfo"
    ]

    // Values de configuración
    static values = {
        // Configuración del formulario
        validateOnChange: { type: Boolean, default: true },
        autoSave: { type: Boolean, default: false },
        autoSaveInterval: { type: Number, default: 30 }, // segundos
        
        // Configuración de validación
        requiredFields: { type: Array, default: ["name", "cedula"] },
        minAge: { type: Number, default: 0 },
        maxAge: { type: Number, default: 120 },
        
        // Estado del formulario
        patientId: String,
        isEditing: { type: Boolean, default: false },
        hasChanges: { type: Boolean, default: false }
    }

    // Classes CSS configurables
    static classes = [
        "loading", "error", "success", "warning",
        "fieldError", "fieldValid", "disabled"
    ]

    connect() {
        console.log("🏥 [Internal] Patient controller conectado")
        
        this.setupEventListeners()
        this.setupValidation()
        
        if (this.autoSaveValue && this.isEditingValue) {
            this.startAutoSave()
        }
    }

    disconnect() {
        this.stopAutoSave()
        this.removeEventListeners()
    }

    // ======================================
    // EVENTOS Y CONFIGURACIÓN
    // ======================================

    setupEventListeners() {
        // Escuchar cambios en los campos si está habilitado
        if (this.validateOnChangeValue) {
            this.element.addEventListener('input', this.handleFieldChange.bind(this))
            this.element.addEventListener('blur', this.handleFieldBlur.bind(this))
        }

        // Escuchar antes de salir de la página si hay cambios
        window.addEventListener('beforeunload', this.handleBeforeUnload.bind(this))
    }

    removeEventListeners() {
        window.removeEventListener('beforeunload', this.handleBeforeUnload.bind(this))
    }

    setupValidation() {
        // Configurar validación inicial
        this.validateAllFields()
    }

    // ======================================
    // MANEJO DE FORMULARIOS
    // ======================================

    // Action: Enviar formulario
    async submitForm(event) {
        event.preventDefault()
        
        if (!this.validateAllFields()) {
            this.showError("Por favor corrige los errores antes de continuar")
            return
        }

        this.showLoading("Guardando paciente...")
        
        try {
            const formData = this.collectFormData()
            const result = await this.savePatient(formData)
            
            this.showSuccess("Paciente guardado exitosamente")
            this.resetChangesFlag()
            
            // Opcional: redirigir o limpiar formulario
            if (result.redirect) {
                window.location.href = result.redirect
            }
            
        } catch (error) {
            this.showError(`Error al guardar: ${error.message}`)
        } finally {
            this.hideLoading()
        }
    }

    // Action: Cancelar edición
    cancel() {
        if (this.hasChangesValue) {
            if (!confirm("¿Estás seguro? Se perderán los cambios no guardados.")) {
                return
            }
        }
        
        this.resetForm()
        this.resetChangesFlag()
    }

    // Action: Limpiar formulario
    clearForm() {
        if (this.hasChangesValue) {
            if (!confirm("¿Limpiar todos los campos?")) {
                return
            }
        }
        
        this.resetForm()
        this.resetChangesFlag()
    }

    // ======================================
    // VALIDACIÓN
    // ======================================

    validateAllFields() {
        let isValid = true
        
        // Validar campos requeridos
        this.requiredFieldsValue.forEach(fieldName => {
            if (!this.validateRequiredField(fieldName)) {
                isValid = false
            }
        })
        
        // Validar formato de cédula
        if (!this.validateCedula()) {
            isValid = false
        }
        
        // Validar email
        if (!this.validateEmail()) {
            isValid = false
        }
        
        // Validar edad
        if (!this.validateAge()) {
            isValid = false
        }
        
        return isValid
    }

    validateRequiredField(fieldName) {
        if (!this.hasTarget(fieldName)) return true
        
        const field = this[`${fieldName}Target`]
        const value = field.value.trim()
        
        if (!value) {
            this.markFieldAsError(field, "Este campo es requerido")
            return false
        }
        
        this.markFieldAsValid(field)
        return true
    }

    validateCedula() {
        if (!this.hasCedulaTarget) return true
        
        const cedula = this.cedulaTarget.value.trim()
        if (!cedula) return true // Ya se valida en required
        
        // Validación básica de cédula (formato numérico)
        if (!/^\d{7,8}$/.test(cedula)) {
            this.markFieldAsError(this.cedulaTarget, "Cédula debe tener 7-8 dígitos")
            return false
        }
        
        this.markFieldAsValid(this.cedulaTarget)
        return true
    }

    validateEmail() {
        if (!this.hasEmailTarget) return true
        
        const email = this.emailTarget.value.trim()
        if (!email) return true // Email opcional
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        if (!emailRegex.test(email)) {
            this.markFieldAsError(this.emailTarget, "Email inválido")
            return false
        }
        
        this.markFieldAsValid(this.emailTarget)
        return true
    }

    validateAge() {
        if (!this.hasBirthDateTarget) return true
        
        const birthDate = this.birthDateTarget.value
        if (!birthDate) return true
        
        const age = this.calculateAge(birthDate)
        
        if (age < this.minAgeValue || age > this.maxAgeValue) {
            this.markFieldAsError(this.birthDateTarget, 
                `Edad debe estar entre ${this.minAgeValue} y ${this.maxAgeValue} años`)
            return false
        }
        
        this.markFieldAsValid(this.birthDateTarget)
        return true
    }

    // ======================================
    // UTILIDADES
    // ======================================

    collectFormData() {
        const data = {}
        
        // Recopilar datos de todos los targets que son inputs
        Object.getOwnPropertyNames(this.constructor)
            .filter(prop => prop.endsWith('Targets'))
            .forEach(prop => {
                const targetName = prop.replace('Targets', '')
                if (this.hasTarget(targetName)) {
                    const element = this[`${targetName}Target`]
                    if (element.tagName === 'INPUT' || element.tagName === 'SELECT' || element.tagName === 'TEXTAREA') {
                        data[targetName] = element.value
                    }
                }
            })
        
        return data
    }

    async savePatient(formData) {
        const url = this.isEditingValue ? 
            `/patients/${this.patientIdValue}` : 
            '/patients'
        
        const method = this.isEditingValue ? 'PUT' : 'POST'
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(formData)
        })
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`)
        }
        
        return await response.json()
    }

    calculateAge(birthDate) {
        const today = new Date()
        const birth = new Date(birthDate)
        let age = today.getFullYear() - birth.getFullYear()
        const monthDiff = today.getMonth() - birth.getMonth()
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--
        }
        
        return age
    }

    // ======================================
    // UI Y FEEDBACK
    // ======================================

    markFieldAsError(field, message) {
        field.classList.remove(this.fieldValidClass)
        field.classList.add(this.fieldErrorClass)
        
        // Mostrar mensaje de error
        this.showFieldError(field, message)
    }

    markFieldAsValid(field) {
        field.classList.remove(this.fieldErrorClass)
        field.classList.add(this.fieldValidClass)
        
        // Ocultar mensaje de error
        this.hideFieldError(field)
    }

    showFieldError(field, message) {
        // Buscar o crear contenedor de error
        let errorContainer = field.parentNode.querySelector('.field-error')
        if (!errorContainer) {
            errorContainer = document.createElement('div')
            errorContainer.className = 'field-error text-red-600 text-sm mt-1'
            field.parentNode.appendChild(errorContainer)
        }
        
        errorContainer.textContent = message
    }

    hideFieldError(field) {
        const errorContainer = field.parentNode.querySelector('.field-error')
        if (errorContainer) {
            errorContainer.remove()
        }
    }

    showLoading(message = "Cargando...") {
        if (this.hasLoadingTarget) {
            this.loadingTarget.textContent = message
            this.loadingTarget.classList.remove('hidden')
        }
        
        if (this.hasSubmitButtonTarget) {
            this.submitButtonTarget.disabled = true
            this.submitButtonTarget.classList.add(this.disabledClass)
        }
    }

    hideLoading() {
        if (this.hasLoadingTarget) {
            this.loadingTarget.classList.add('hidden')
        }
        
        if (this.hasSubmitButtonTarget) {
            this.submitButtonTarget.disabled = false
            this.submitButtonTarget.classList.remove(this.disabledClass)
        }
    }

    showError(message) {
        if (this.hasErrorTarget) {
            this.errorTarget.textContent = message
            this.errorTarget.classList.remove('hidden')
        }
        
        // Auto-ocultar después de 5 segundos
        setTimeout(() => this.hideError(), 5000)
    }

    hideError() {
        if (this.hasErrorTarget) {
            this.errorTarget.classList.add('hidden')
        }
    }

    showSuccess(message) {
        if (this.hasSuccessTarget) {
            this.successTarget.textContent = message
            this.successTarget.classList.remove('hidden')
        }
        
        // Auto-ocultar después de 3 segundos
        setTimeout(() => this.hideSuccess(), 3000)
    }

    hideSuccess() {
        if (this.hasSuccessTarget) {
            this.successTarget.classList.add('hidden')
        }
    }

    // ======================================
    // AUTO-SAVE
    // ======================================

    startAutoSave() {
        this.autoSaveTimer = setInterval(() => {
            if (this.hasChangesValue) {
                this.autoSaveForm()
            }
        }, this.autoSaveIntervalValue * 1000)
    }

    stopAutoSave() {
        if (this.autoSaveTimer) {
            clearInterval(this.autoSaveTimer)
            this.autoSaveTimer = null
        }
    }

    async autoSaveForm() {
        if (!this.validateAllFields()) return
        
        try {
            const formData = this.collectFormData()
            await this.savePatient(formData)
            
            console.log("🏥 [Internal] Auto-guardado exitoso")
            this.resetChangesFlag()
            
        } catch (error) {
            console.error("🏥 [Internal] Error en auto-guardado:", error)
        }
    }

    // ======================================
    // MANEJO DE CAMBIOS
    // ======================================

    handleFieldChange(event) {
        this.hasChangesValue = true
        
        // Validar campo específico
        if (this.validateOnChangeValue) {
            this.validateField(event.target)
        }
    }

    handleFieldBlur(event) {
        this.validateField(event.target)
    }

    handleBeforeUnload(event) {
        if (this.hasChangesValue) {
            event.preventDefault()
            event.returnValue = 'Tienes cambios sin guardar. ¿Estás seguro de salir?'
            return event.returnValue
        }
    }

    validateField(field) {
        const fieldName = field.getAttribute('data-internal--patient-target')
        if (!fieldName) return
        
        switch (fieldName) {
            case 'cedula':
                this.validateCedula()
                break
            case 'email':
                this.validateEmail()
                break
            case 'birthDate':
                this.validateAge()
                break
            default:
                if (this.requiredFieldsValue.includes(fieldName)) {
                    this.validateRequiredField(fieldName)
                }
        }
    }

    resetForm() {
        // Limpiar todos los campos
        this.element.querySelectorAll('input, select, textarea').forEach(field => {
            if (field.type === 'checkbox' || field.type === 'radio') {
                field.checked = false
            } else {
                field.value = ''
            }
            
            // Limpiar clases de validación
            field.classList.remove(this.fieldErrorClass, this.fieldValidClass)
        })
        
        // Limpiar mensajes
        this.element.querySelectorAll('.field-error').forEach(error => error.remove())
        this.hideError()
        this.hideSuccess()
    }

    resetChangesFlag() {
        this.hasChangesValue = false
    }

    // ======================================
    // CALLBACKS DE VALUES
    // ======================================

    hasChangesValueChanged(newValue) {
        // Actualizar UI basado en si hay cambios
        if (this.hasSubmitButtonTarget) {
            this.submitButtonTarget.disabled = !newValue
        }
    }

    autoSaveValueChanged(newValue) {
        if (newValue && this.isEditingValue) {
            this.startAutoSave()
        } else {
            this.stopAutoSave()
        }
    }
}