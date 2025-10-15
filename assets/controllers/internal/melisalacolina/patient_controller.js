import PatientController from "../default/patient_controller.js"

/**
 * Controller interno específico para Clínica La Colina
 * 
 * Extiende el controller base interno con funcionalidades
 * específicas de La Colina para formularios y UI
 */
export default class extends PatientController {
    
    // Targets adicionales específicos de La Colina
    static targets = [
        ...PatientController.targets,
        "specialty", "referringDoctor", "insuranceProvider"
    ]

    // Values específicos de La Colina
    static values = {
        ...PatientController.values,
        enableSpecialtySelection: { type: Boolean, default: true },
        enableInsuranceValidation: { type: Boolean, default: true },
        clinicTheme: { type: String, default: "green" }
    }

    connect() {
        super.connect()
        console.log("🏥 [Internal La Colina] Controller conectado")
        
        this.setupClinicTheme()
        this.setupSpecialtyFeatures()
    }

    // ======================================
    // CONFIGURACIÓN ESPECÍFICA
    // ======================================

    setupClinicTheme() {
        // Aplicar tema verde de La Colina
        document.documentElement.style.setProperty('--clinic-primary', '#059669')
        document.documentElement.style.setProperty('--clinic-secondary', '#ecfdf5')
        
        // Agregar clase al body para estilos específicos
        document.body.classList.add('clinic-lacolina')
    }

    setupSpecialtyFeatures() {
        if (this.enableSpecialtySelectionValue && this.hasSpecialtyTarget) {
            this.populateSpecialtyOptions()
        }
    }

    // ======================================
    // VALIDACIÓN ESPECÍFICA
    // ======================================

    validateAllFields() {
        let isValid = super.validateAllFields()
        
        // Validaciones adicionales específicas de La Colina
        if (this.enableInsuranceValidationValue) {
            if (!this.validateInsurance()) {
                isValid = false
            }
        }
        
        if (!this.validateSpecialty()) {
            isValid = false
        }
        
        return isValid
    }

    validateInsurance() {
        if (!this.hasInsuranceProviderTarget) return true
        
        const insurance = this.insuranceProviderTarget.value.trim()
        if (!insurance) {
            this.markFieldAsError(this.insuranceProviderTarget, "Selecciona un proveedor de seguros")
            return false
        }
        
        this.markFieldAsValid(this.insuranceProviderTarget)
        return true
    }

    validateSpecialty() {
        if (!this.hasSpecialtyTarget) return true
        
        const specialty = this.specialtyTarget.value.trim()
        if (!specialty) {
            this.markFieldAsError(this.specialtyTarget, "Selecciona una especialidad")
            return false
        }
        
        this.markFieldAsValid(this.specialtyTarget)
        return true
    }

    // ======================================
    // FUNCIONALIDADES ESPECÍFICAS
    // ======================================

    populateSpecialtyOptions() {
        const specialties = [
            'Medicina General',
            'Cardiología',
            'Neurología', 
            'Ginecología',
            'Dermatología',
            'Traumatología',
            'Pediatría',
            'Oftalmología'
        ]
        
        if (this.specialtyTarget.tagName === 'SELECT') {
            // Limpiar opciones existentes
            this.specialtyTarget.innerHTML = '<option value="">Seleccionar especialidad...</option>'
            
            // Agregar opciones
            specialties.forEach(specialty => {
                const option = document.createElement('option')
                option.value = specialty.toLowerCase()
                option.textContent = specialty
                this.specialtyTarget.appendChild(option)
            })
        }
    }

    // Action: Cambiar especialidad
    changeSpecialty(event) {
        const specialty = event.target.value
        console.log(`🏥 [Internal La Colina] Especialidad seleccionada: ${specialty}`)
        
        // Actualizar otros campos basado en la especialidad
        this.updateFieldsForSpecialty(specialty)
    }

    updateFieldsForSpecialty(specialty) {
        // Lógica específica según la especialidad seleccionada
        switch (specialty) {
            case 'cardiologia':
                this.showCardiacFields()
                break
            case 'pediatria':
                this.showPediatricFields()
                break
            case 'ginecologia':
                this.showGynecologyFields()
                break
            default:
                this.showGeneralFields()
        }
    }

    showCardiacFields() {
        // Mostrar campos específicos para cardiología
        console.log("🏥 [Internal La Colina] Mostrando campos de cardiología")
        // Aquí irían campos específicos como presión arterial, etc.
    }

    showPediatricFields() {
        // Mostrar campos específicos para pediatría
        console.log("🏥 [Internal La Colina] Mostrando campos de pediatría")
        // Campos como peso, talla, vacunas, etc.
    }

    showGynecologyFields() {
        // Mostrar campos específicos para ginecología
        console.log("🏥 [Internal La Colina] Mostrando campos de ginecología")
        // Campos específicos de ginecología
    }

    showGeneralFields() {
        // Mostrar campos generales
        console.log("🏥 [Internal La Colina] Mostrando campos generales")
    }

    // ======================================
    // OVERRIDE DE MÉTODOS BASE
    // ======================================

    collectFormData() {
        const baseData = super.collectFormData()
        
        // Agregar datos específicos de La Colina
        return {
            ...baseData,
            clinic: 'melisalacolina',
            theme: this.clinicThemeValue,
            hasSpecialtyValidation: this.enableSpecialtySelectionValue
        }
    }

    async savePatient(formData) {
        // Agregar headers específicos de La Colina
        const url = this.isEditingValue ? 
            `/patients/${this.patientIdValue}` : 
            '/patients'
        
        const method = this.isEditingValue ? 'PUT' : 'POST'
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-Clinic-Context': 'melisalacolina',
                'X-Specialty-Required': this.enableSpecialtySelectionValue ? 'true' : 'false'
            },
            body: JSON.stringify(formData)
        })
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`)
        }
        
        return await response.json()
    }

    showSuccess(message) {
        // Mostrar mensaje de éxito con estilo de La Colina
        const clinicMessage = `🏥 ${message} - Clínica La Colina`
        super.showSuccess(clinicMessage)
    }

    // ======================================
    // ACTIONS ESPECÍFICAS
    // ======================================

    // Action: Buscar doctor de referencia
    searchReferringDoctor(event) {
        const query = event.target.value
        if (query.length >= 2) {
            this.performDoctorSearch(query)
        }
    }

    async performDoctorSearch(query) {
        try {
            const response = await fetch(`/api/doctors/search?q=${encodeURIComponent(query)}&clinic=melisalacolina`)
            const doctors = await response.json()
            
            this.displayDoctorSuggestions(doctors)
        } catch (error) {
            console.error("🏥 [Internal La Colina] Error buscando doctores:", error)
        }
    }

    displayDoctorSuggestions(doctors) {
        // Crear dropdown con sugerencias de doctores
        let dropdown = this.element.querySelector('.doctor-suggestions')
        if (!dropdown) {
            dropdown = document.createElement('div')
            dropdown.className = 'doctor-suggestions absolute bg-white border border-gray-300 rounded-md shadow-lg z-10'
            this.referringDoctorTarget.parentNode.appendChild(dropdown)
        }
        
        dropdown.innerHTML = doctors.map(doctor => 
            `<div class="p-2 hover:bg-gray-100 cursor-pointer" data-action="click->internal--melisalacolina--patient#selectDoctor" data-doctor-id="${doctor.id}">
                <strong>${doctor.name}</strong><br>
                <small class="text-gray-600">${doctor.specialty}</small>
            </div>`
        ).join('')
    }

    // Action: Seleccionar doctor
    selectDoctor(event) {
        const doctorName = event.target.querySelector('strong').textContent
        this.referringDoctorTarget.value = doctorName
        
        // Ocultar dropdown
        const dropdown = this.element.querySelector('.doctor-suggestions')
        if (dropdown) {
            dropdown.remove()
        }
    }
}