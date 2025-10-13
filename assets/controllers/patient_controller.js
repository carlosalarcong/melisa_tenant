import { Controller } from "@hotwired/stimulus"

// Conecta con data-controller="patient"
export default class extends Controller {
    // Define los "targets" - elementos que queremos controlar
    static targets = ["info", "name", "status", "age", "phone", "address", "bloodType", "allergies", "loading", "error"]
    
    // Define los "values" - datos que el controlador puede recibir desde HTML
    static values = { 
        patientId: Number,
        isActive: Boolean,
        apiUrl: String  // Nueva: URL base de la API
    }

    // Este método se ejecuta cuando el controlador se conecta al DOM
    connect() {
        console.log("🏥 Controlador de paciente conectado!")
        console.log("Patient ID:", this.patientIdValue)
        console.log("¿Está activo?", this.isActiveValue)
        console.log("API URL:", this.apiUrlValue)
    }

    // 🚀 MÉTODO MEJORADO: Carga datos desde la API
    async showInfo() {
        try {
            // Mostrar indicador de carga
            this.showLoading()
            this.hideError()
            
            // 📡 Llamada a la API usando Fetch
            const response = await fetch(`${this.apiUrlValue}/${this.patientIdValue}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' // Para que Symfony sepa que es AJAX
                }
            })

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`)
            }

            const data = await response.json()
            
            if (data.success) {
                // 📊 Actualizar la UI con datos reales de la BD
                this.displayPatientData(data.patient)
                console.log("📋 Datos del paciente cargados desde API:", data.patient)
            } else {
                throw new Error(data.error || 'Error desconocido')
            }

        } catch (error) {
            console.error("❌ Error cargando paciente:", error)
            this.showError(`Error: ${error.message}`)
        } finally {
            this.hideLoading()
        }
    }

    // 🎨 Método para mostrar los datos en la UI
    displayPatientData(patient) {
        this.infoTarget.style.display = "block"
        
        // Datos básicos
        this.nameTarget.textContent = patient.name || "Sin nombre"
        this.statusTarget.textContent = patient.status || "Sin estado"
        this.ageTarget.textContent = `${patient.age} años` || "Sin edad"
        
        // Datos adicionales (si existen los targets)
        if (this.hasPhoneTarget) {
            this.phoneTarget.textContent = patient.phone || "Sin teléfono"
        }
        
        if (this.hasAddressTarget) {
            this.addressTarget.textContent = patient.address || "Sin dirección"
        }
        
        if (this.hasBloodTypeTarget) {
            this.bloodTypeTarget.textContent = patient.bloodType || "Sin tipo"
        }
        
        if (this.hasAllergiesTarget) {
            const allergiesText = patient.allergies && patient.allergies.length > 0 
                ? patient.allergies.join(', ') 
                : 'Sin alergias conocidas'
            this.allergiesTarget.textContent = allergiesText
        }
    }

    // 🧹 Método para limpiar la información
    clearInfo() {
        this.infoTarget.style.display = "none"
        this.clearAllFields()
        this.hideError()
        console.log("🧹 Información limpiada")
    }

    // 🔄 Método para cambiar ID de paciente dinámicamente
    async changePatient(event) {
        const newId = event.target.dataset.patientId
        if (newId && newId !== this.patientIdValue.toString()) {
            this.patientIdValue = parseInt(newId)
            await this.showInfo()
        }
    }

    // 🔍 Método para buscar pacientes
    async searchPatients(event) {
        const query = event.target.value.trim()
        
        if (query.length < 2) return // No buscar con menos de 2 caracteres
        
        try {
            const response = await fetch(`${this.apiUrlValue}/search?q=${encodeURIComponent(query)}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            
            const data = await response.json()
            
            if (data.success) {
                console.log("🔍 Resultados de búsqueda:", data.patients)
                // Aquí podrías actualizar una lista de sugerencias
                this.displaySearchResults(data.patients)
            }
            
        } catch (error) {
            console.error("❌ Error en búsqueda:", error)
        }
    }

    // 📋 Métodos auxiliares para manejo de UI
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
        }
    }

    hideError() {
        if (this.hasErrorTarget) {
            this.errorTarget.style.display = "none"
        }
    }

    clearAllFields() {
        this.nameTarget.textContent = ""
        this.statusTarget.textContent = ""
        this.ageTarget.textContent = ""
        
        if (this.hasPhoneTarget) this.phoneTarget.textContent = ""
        if (this.hasAddressTarget) this.addressTarget.textContent = ""
        if (this.hasBloodTypeTarget) this.bloodTypeTarget.textContent = ""
        if (this.hasAllergiesTarget) this.allergiesTarget.textContent = ""
    }

    displaySearchResults(patients) {
        // Este método se puede expandir para mostrar resultados de búsqueda
        console.log(`Encontrados ${patients.length} pacientes`)
    }

    // Método que se ejecuta cuando cambia un valor
    patientIdValueChanged() {
        console.log("🆔 ID del paciente cambió a:", this.patientIdValue)
    }
}