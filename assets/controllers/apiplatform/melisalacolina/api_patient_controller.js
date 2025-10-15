import ApiPatientController from "../default/api_patient_controller.js"

/**
 * Controlador especializado para Clínica La Colina
 * 
 * Extiende el controlador base con funcionalidades específicas
 * para consultas especializadas y tratamientos clínicos
 */
export default class extends ApiPatientController {
    
    // Targets adicionales específicos de La Colina
    static targets = [
        ...ApiPatientController.targets,
        "specialty", "referringDoctor", "insuranceProvider",
        "appointmentHistory", "treatmentPlan"
    ]

    // Values específicos de La Colina
    static values = {
        ...ApiPatientController.values,
        showSpecialties: { type: Boolean, default: true },
        enableInsuranceInfo: { type: Boolean, default: true }
    }

    connect() {
        super.connect()
        this.log("🏥 Controlador La Colina especializado conectado")
        
        // Configuración específica de La Colina
        this.setupClinicSpecificFeatures()
    }

    // 🎨 Override: Crear card con información específica de clínica
    createPatientCard(patient) {
        const card = super.createPatientCard(patient)
        
        // Agregar información específica de clínica
        if (patient.tenant === 'melisalacolina') {
            const specialtyInfo = this.createSpecialtyBadge(patient)
            if (specialtyInfo) {
                card.querySelector('.flex-1').appendChild(specialtyInfo)
            }
        }
        
        return card
    }

    // 🏥 Crear badge de especialidad
    createSpecialtyBadge(patient) {
        const specialties = this.extractSpecialtyFromName(patient.name)
        if (!specialties.length) return null
        
        const badge = document.createElement('div')
        badge.className = 'mt-2'
        badge.innerHTML = specialties.map(specialty => 
            `<span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-1">
                ${specialty}
            </span>`
        ).join('')
        
        return badge
    }

    // 🔍 Extraer especialidad del nombre del paciente
    extractSpecialtyFromName(name) {
        const specialties = [
            'Cardiología', 'Neurología', 'Ginecología', 
            'Dermatología', 'Traumatología', 'Pediatría'
        ]
        
        return specialties.filter(specialty => 
            name.toLowerCase().includes(specialty.toLowerCase())
        )
    }

    // 📊 Override: Mostrar información con datos clínicos
    displayPatientInfo(patient) {
        super.displayPatientInfo(patient)
        
        // Información específica de clínica
        if (this.showSpecialtiesValue) {
            this.displaySpecialtyInfo(patient)
        }
        
        if (this.enableInsuranceInfoValue) {
            this.displayInsuranceInfo(patient)
        }
    }

    // 🏥 Mostrar información de especialidad
    displaySpecialtyInfo(patient) {
        const specialties = this.extractSpecialtyFromName(patient.name)
        this.updateTarget('specialty', specialties.join(', ') || 'Medicina General')
    }

    // 💳 Mostrar información de seguros (simulada)
    displayInsuranceInfo(patient) {
        // Simular información de seguros basada en datos del paciente
        const insuranceInfo = this.generateInsuranceInfo(patient)
        this.updateTarget('insuranceProvider', insuranceInfo.provider)
    }

    // 💳 Generar información de seguros simulada
    generateInsuranceInfo(patient) {
        const insuranceProviders = [
            'Isapre Banmédica', 'Isapre Colmena', 'Isapre Cruz Blanca',
            'Fonasa Grupo A', 'Fonasa Grupo B', 'Fonasa Grupo C'
        ]
        
        // Usar una simulación basada en el ID del paciente para consistencia
        const index = patient.id.charCodeAt(patient.id.length - 1) % insuranceProviders.length
        
        return {
            provider: insuranceProviders[index],
            plan: 'Plan Clínico Completo',
            coverage: '80%'
        }
    }

    // 🔧 Configurar características específicas de la clínica
    setupClinicSpecificFeatures() {
        // Configurar colores específicos de La Colina
        document.documentElement.style.setProperty('--clinic-primary', '#059669')
        document.documentElement.style.setProperty('--clinic-secondary', '#ecfdf5')
        
        // Agregar estilos específicos
        this.addClinicStyles()
    }

    // 🎨 Agregar estilos CSS específicos
    addClinicStyles() {
        const style = document.createElement('style')
        style.textContent = `
            .lacolina-patient-card {
                border-left: 4px solid #059669;
            }
            .lacolina-specialty-badge {
                background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
                color: #059669;
            }
        `
        document.head.appendChild(style)
    }

    // 🔍 Override: Búsqueda con filtros de especialidad
    filterPatients(patients, query) {
        const baseResults = super.filterPatients(patients, query)
        
        // Si la query incluye una especialidad, filtrar por ella
        const specialties = ['cardio', 'neuro', 'gineco', 'dermato', 'trauma']
        const specialtyQuery = specialties.find(s => 
            query.toLowerCase().includes(s)
        )
        
        if (specialtyQuery) {
            return baseResults.filter(patient =>
                patient.name.toLowerCase().includes(specialtyQuery)
            )
        }
        
        return baseResults
    }

    log(message) {
        if (this.debugModeValue) {
            console.log(`🏥 [La Colina] ${message}`)
        }
    }
}