import { Controller } from "@hotwired/stimulus"

/**
 * Controller específico para Hospital - Demostración Stimulus
 * 
 * Este controller demuestra la integración con la base de datos
 * específica del tenant melisahospital
 */
export default class extends Controller {
    
    // Targets para elementos del DOM
    static targets = [
        "info", "name", "status", "age", "phone", "address", 
        "bloodType", "allergies", "loading", "error"
    ]

    // Values de configuración
    static values = {
        patientId: { type: String, default: "12345" },
        isEditing: { type: Boolean, default: false },
        validateOnChange: { type: Boolean, default: true },
        apiUrl: { type: String, default: "/api/patients" }
    }

    connect() {
        console.log("🏥 [Hospital] Patient controller conectado")
        console.log("📊 Tenant: melisahospital")
        console.log("🆔 Patient ID inicial:", this.patientIdValue)
    }

    // ======================================
    // ACCIONES PRINCIPALES
    // ======================================

    async showInfo(event) {
        event.preventDefault()
        
        console.log("📋 Cargando información del paciente:", this.patientIdValue)
        
        this.showLoading()
        this.hideError()

        try {
            // Simular datos del paciente desde la base de datos melisahospital
            const patientData = await this.fetchPatientData(this.patientIdValue)
            
            this.displayPatientData(patientData)
            this.showInfo_target()
            
        } catch (error) {
            this.showError(`Error al cargar paciente: ${error.message}`)
        } finally {
            this.hideLoading()
        }
    }

    clearInfo(event) {
        event.preventDefault()
        
        console.log("🧹 Limpiando información del paciente")
        
        this.hideInfo_target()
        this.clearAllFields()
        this.hideError()
    }

    async searchPatients(event) {
        const searchTerm = event.target.value.trim()
        
        if (searchTerm.length < 3) {
            return
        }
        
        console.log("🔍 Buscando pacientes:", searchTerm)
        
        try {
            // Simular búsqueda en la base de datos
            const results = await this.performSearch(searchTerm)
            console.log("📊 Resultados encontrados:", results.length)
            
            // Aquí podrías mostrar los resultados en un dropdown o lista
            
        } catch (error) {
            console.error("❌ Error en búsqueda:", error)
        }
    }

    async changePatient(event) {
        event.preventDefault()
        
        const newPatientId = event.target.dataset.patientId
        console.log("👤 Cambiando a paciente:", newPatientId)
        
        this.patientIdValue = newPatientId
        
        // Cargar automáticamente la información del nuevo paciente
        await this.showInfo(event)
    }

    // ======================================
    // SIMULACIÓN DE API / BASE DE DATOS
    // ======================================

    async fetchPatientData(patientId) {
        // Simular llamada a la API que consulta la base de datos melisahospital
        console.log("🌐 Simulando fetch a:", `${this.apiUrlValue}/${patientId}`)
        
        // Simular delay de red
        await this.delay(800)
        
        // Datos de ejemplo que vendrían de la base de datos
        const patients = {
            "12345": {
                id: "12345",
                name: "Juan Pérez González",
                age: 45,
                status: "Activo",
                phone: "+56 9 8765 4321",
                address: "Av. Providencia 1234, Santiago",
                bloodType: "O+",
                allergies: ["Penicilina", "Mariscos"],
                tenant: "melisahospital"
            },
            "67890": {
                id: "67890", 
                name: "Ana Rodríguez Silva",
                age: 32,
                status: "En Tratamiento",
                phone: "+56 9 1234 5678",
                address: "Las Condes 5678, Santiago",
                bloodType: "A-",
                allergies: ["Aspirina"],
                tenant: "melisahospital"
            }
        }
        
        const patient = patients[patientId]
        
        if (!patient) {
            throw new Error(`Paciente ${patientId} no encontrado en melisahospital`)
        }
        
        return { patient, success: true }
    }

    async performSearch(searchTerm) {
        console.log("🔍 Simulando búsqueda en BD melisahospital para:", searchTerm)
        
        // Simular delay de búsqueda
        await this.delay(300)
        
        // Simular resultados de búsqueda
        const allPatients = [
            { id: "12345", name: "Juan Pérez González" },
            { id: "67890", name: "Ana Rodríguez Silva" },
            { id: "11111", name: "Carlos Mendoza López" },
            { id: "22222", name: "María González Torres" }
        ]
        
        return allPatients.filter(patient => 
            patient.name.toLowerCase().includes(searchTerm.toLowerCase())
        )
    }

    // ======================================
    // ACTUALIZACIÓN DE UI
    // ======================================

    displayPatientData(data) {
        const patient = data.patient
        
        console.log("📊 Mostrando datos del paciente:", patient.name)
        
        // Actualizar todos los targets con los datos
        this.updateTarget("name", patient.name)
        this.updateTarget("status", patient.status)
        this.updateTarget("age", `${patient.age} años`)
        this.updateTarget("phone", patient.phone)
        this.updateTarget("address", patient.address)
        this.updateTarget("bloodType", patient.bloodType)
        this.updateTarget("allergies", patient.allergies.join(", "))
        
        // Actualizar clases CSS basadas en el estado
        if (this.hasStatusTarget) {
            this.statusTarget.className = this.getStatusBadgeClass(patient.status)
        }
        
        if (this.hasBloodTypeTarget) {
            this.bloodTypeTarget.className = "badge bg-danger"
        }
    }

    updateTarget(targetName, value) {
        if (this.hasTarget(targetName)) {
            this[`${targetName}Target`].textContent = value
        }
    }

    getStatusBadgeClass(status) {
        const statusClasses = {
            "Activo": "badge bg-success",
            "En Tratamiento": "badge bg-warning",
            "Inactivo": "badge bg-secondary",
            "Crítico": "badge bg-danger"
        }
        
        return statusClasses[status] || "badge bg-info"
    }

    clearAllFields() {
        const targets = ["name", "status", "age", "phone", "address", "bloodType", "allergies"]
        
        targets.forEach(targetName => {
            if (this.hasTarget(targetName)) {
                this[`${targetName}Target`].textContent = ""
            }
        })
    }

    // ======================================
    // MANEJO DE ESTADOS DE UI
    // ======================================

    showInfo_target() {
        if (this.hasInfoTarget) {
            this.infoTarget.style.display = "block"
            
            // Animación suave
            this.infoTarget.style.opacity = "0"
            setTimeout(() => {
                this.infoTarget.style.opacity = "1"
                this.infoTarget.style.transition = "opacity 0.3s ease"
            }, 10)
        }
    }

    hideInfo_target() {
        if (this.hasInfoTarget) {
            this.infoTarget.style.display = "none"
        }
    }

    showLoading() {
        if (this.hasLoadingTarget) {
            this.loadingTarget.style.display = "block"
        }
    }

    hideLoading() {
        if (this.hasLoadingTarget) {
            this.loadingTarget.style.display = "none"
        }
    }

    showError(message) {
        if (this.hasErrorTarget) {
            this.errorTarget.textContent = message
            this.errorTarget.style.display = "block"
            this.errorTarget.className = "mt-3 alert alert-danger"
        }
        
        console.error("❌ [Hospital]", message)
    }

    hideError() {
        if (this.hasErrorTarget) {
            this.errorTarget.style.display = "none"
        }
    }

    // ======================================
    // UTILIDADES
    // ======================================

    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms))
    }

    hasTarget(targetName) {
        return this[`has${targetName.charAt(0).toUpperCase() + targetName.slice(1)}Target`]
    }

    // ======================================
    // CALLBACKS DE VALUES
    // ======================================

    patientIdValueChanged(newValue, oldValue) {
        if (oldValue !== undefined && newValue !== oldValue) {
            console.log("🆔 Patient ID cambió:", oldValue, "→", newValue)
        }
    }

    // ======================================
    // DEBUG Y LOGGING
    // ======================================

    disconnect() {
        console.log("🏥 [Hospital] Patient controller desconectado")
    }
}