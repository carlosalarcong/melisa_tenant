import { Controller } from "@hotwired/stimulus"

// Conecta con data-controller="patient"
export default class extends Controller {
    // Define los "targets" - elementos que queremos controlar
    static targets = ["info", "name", "status", "age"]
    
    // Define los "values" - datos que el controlador puede recibir desde HTML
    static values = { 
        patientId: Number,
        isActive: Boolean 
    }

    // Este método se ejecuta cuando el controlador se conecta al DOM
    connect() {
        console.log("🏥 Controlador de paciente conectado!")
        console.log("Patient ID:", this.patientIdValue)
        console.log("¿Está activo?", this.isActiveValue)
    }

    // Método que se ejecuta cuando se hace clic en "Mostrar Info"
    showInfo() {
        // Accedemos a los targets usando this.nombreTarget
        this.infoTarget.style.display = "block"
        this.nameTarget.textContent = "Juan Pérez González"
        this.statusTarget.textContent = "Activo"
        this.ageTarget.textContent = "45 años"
        
        console.log("📋 Información del paciente mostrada")
    }

    // Método para limpiar la información
    clearInfo() {
        this.infoTarget.style.display = "none"
        this.nameTarget.textContent = ""
        this.statusTarget.textContent = ""
        this.ageTarget.textContent = ""
        
        console.log("🧹 Información limpiada")
    }

    // Método que se ejecuta cuando cambia un valor
    patientIdValueChanged() {
        console.log("🆔 ID del paciente cambió a:", this.patientIdValue)
    }
}