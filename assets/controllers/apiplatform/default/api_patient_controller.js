import { Controller } from "@hotwired/stimulus"

/**
 * Controlador Stimulus especializado para API Platform
 * 
 * Maneja la integración completa con endpoints de API Platform multi-tenant
 * Optimizado para State Providers dinámicos y datos médicos
 */
export default class extends Controller {
    // Targets específicos para datos médicos de API Platform
    static targets = [
        // UI General
        "loading", "error", "searchResults", "patientList", "info",
        
        // Datos del Paciente
        "name", "cedula", "email", "phone", "address", "gender", "birthDate",
        
        // Información Médica
        "bloodType", "allergies", "medications", 
        
        // Contacto de Emergencia
        "emergencyContact", "emergencyPhone",
        
        // Metadatos
        "patientId", "tenant", "createdAt", "updatedAt"
    ]
    
    // Values para configuración de API Platform
    static values = { 
        patientId: String,          // ID del paciente (ej: "HSP001", "COL001", "WIC001")
        apiUrl: String,             // URL base: "/api/patients"
        tenant: String,             // Tenant actual
        autoLoad: Boolean,          // Auto-cargar al conectar
        cacheEnabled: Boolean,      // Habilitar cache local
        debugMode: Boolean          // Modo debug
    }

    // Cache local para optimización
    allPatientsCache = null
    selectedPatientId = null

    // 🚀 Conexión del controlador
    connect() {
        this.log("🏥 API Platform Patient Controller conectado")
        this.log(`📡 Tenant: ${this.tenantValue}`)
        this.log(`🌐 API URL: ${this.apiUrlValue}`)
        
        // Auto-cargar si está habilitado
        if (this.autoLoadValue) {
            this.loadPatientsList()
        }
        
        // Configurar listeners globales
        this.setupGlobalListeners()
    }

    // 📋 Cargar lista completa de pacientes desde API Platform
    async loadPatientsList() {
        try {
            this.showLoading("Cargando pacientes desde API Platform...")
            this.hideError()
            
            const response = await this.fetchFromAPI(this.apiUrlValue)
            const patients = await response.json()
            
            // Cache para optimización
            if (this.cacheEnabledValue) {
                this.allPatientsCache = patients
            }
            
            this.displayPatientsList(patients)
            this.log(`📋 Cargados ${patients.length} pacientes desde API Platform`)

        } catch (error) {
            this.handleError("Error cargando lista de pacientes", error)
        } finally {
            this.hideLoading()
        }
    }

    // 👤 Cargar información de un paciente específico
    async loadPatientInfo(patientId = null) {
        const id = patientId || this.patientIdValue
        
        if (!id) {
            this.showError("No se ha especificado un ID de paciente")
            return
        }

        try {
            this.showLoading(`Cargando información del paciente ${id}...`)
            this.hideError()
            
            const response = await this.fetchFromAPI(`${this.apiUrlValue}/${id}`)
            const patient = await response.json()
            
            this.displayPatientInfo(patient)
            this.selectedPatientId = id
            this.log(`👤 Información cargada para paciente: ${patient.name}`)

        } catch (error) {
            this.handleError(`Error cargando paciente ${id}`, error)
        } finally {
            this.hideLoading()
        }
    }

    // 🎨 Mostrar lista de pacientes con cards dinámicas
    displayPatientsList(patients) {
        if (!this.hasPatientListTarget) return
        
        this.patientListTarget.innerHTML = ''
        
        if (patients.length === 0) {
            this.patientListTarget.innerHTML = this.createEmptyState()
            return
        }
        
        patients.forEach(patient => {
            const patientCard = this.createPatientCard(patient)
            this.patientListTarget.appendChild(patientCard)
        })
        
        this.log(`🎨 Renderizados ${patients.length} cards de pacientes`)
    }

    // 🏷️ Crear card individual de paciente
    createPatientCard(patient) {
        const card = document.createElement('div')
        card.className = 'patient-card border rounded-lg p-4 mb-3 cursor-pointer hover:bg-gray-50 transition-all duration-200'
        card.dataset.action = 'click->apiplatform--patient#selectPatient'
        card.dataset.patientId = patient.id
        
        // Determinar color según el tenant/tipo
        const tenantColors = {
            'melisahospital': 'bg-blue-100 text-blue-800',
            'melisalacolina': 'bg-green-100 text-green-800', 
            'melisawiclinic': 'bg-purple-100 text-purple-800'
        }
        
        const tenantColor = tenantColors[patient.tenant] || 'bg-gray-100 text-gray-800'
        
        // Calcular edad si hay fecha de nacimiento
        const age = this.calculateAge(patient.birthDate)
        
        card.innerHTML = `
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h4 class="font-semibold text-lg text-gray-800 mb-1">
                        ${patient.name}
                    </h4>
                    
                    <div class="space-y-1 text-sm text-gray-600">
                        ${patient.email ? `<p>📧 ${patient.email}</p>` : ''}
                        ${patient.phone ? `<p>📱 ${patient.phone}</p>` : ''}
                        ${patient.bloodType ? `<p>🩸 ${patient.bloodType}</p>` : ''}
                        ${age ? `<p>🎂 ${age} años</p>` : ''}
                    </div>
                    
                    ${this.createAllergiesWarning(patient.allergies)}
                </div>
                
                <div class="text-right ml-4">
                    <span class="${tenantColor} px-2 py-1 rounded text-xs font-medium">
                        ${patient.id}
                    </span>
                    <p class="text-xs text-gray-500 mt-1">
                        ${this.getTenantDisplayName(patient.tenant)}
                    </p>
                </div>
            </div>
        `
        
        return card
    }

    // ⚠️ Crear advertencia de alergias
    createAllergiesWarning(allergies) {
        if (!allergies || allergies.length === 0) return ''
        
        return `
            <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded">
                <p class="text-red-700 text-xs">
                    ⚠️ Alergias: ${allergies.join(', ')}
                </p>
            </div>
        `
    }

    // 📊 Mostrar información detallada del paciente
    displayPatientInfo(patient) {
        if (!this.hasInfoTarget) return
        
        this.infoTarget.style.display = "block"
        
        // Datos básicos
        this.updateTarget('name', patient.name)
        this.updateTarget('cedula', patient.cedula)
        this.updateTarget('email', patient.email)
        this.updateTarget('phone', patient.phone)
        this.updateTarget('address', patient.address)
        
        // Género con formato
        if (this.hasGenderTarget) {
            const genderText = this.formatGender(patient.gender)
            this.genderTarget.textContent = genderText
        }
        
        // Fecha de nacimiento formateada
        if (this.hasBirthDateTarget) {
            const formattedDate = this.formatDate(patient.birthDate)
            this.birthDateTarget.textContent = formattedDate
        }
        
        // Información médica
        this.updateTarget('bloodType', patient.bloodType)
        this.updateTarget('allergies', this.formatArray(patient.allergies, 'Sin alergias conocidas'))
        this.updateTarget('medications', this.formatArray(patient.medications, 'Sin medicamentos'))
        
        // Contacto de emergencia
        this.updateTarget('emergencyContact', patient.emergencyContact)
        this.updateTarget('emergencyPhone', patient.emergencyPhone)
        
        // Metadatos
        this.updateTarget('patientId', patient.id)
        this.updateTarget('tenant', this.getTenantDisplayName(patient.tenant))
        this.updateTarget('createdAt', this.formatDateTime(patient.createdAt))
        this.updateTarget('updatedAt', this.formatDateTime(patient.updatedAt))
        
        this.log(`📊 Información mostrada para: ${patient.name}`)
    }

    // 🖱️ Seleccionar paciente de la lista
    selectPatient(event) {
        const patientId = event.currentTarget.dataset.patientId
        
        // Actualizar selección visual
        this.updateVisualSelection(event.currentTarget)
        
        // Cargar información del paciente
        this.loadPatientInfo(patientId)
        
        // Actualizar valor del controlador
        this.patientIdValue = patientId
        
        this.log(`👆 Paciente seleccionado: ${patientId}`)
    }

    // 🔍 Búsqueda en tiempo real
    async searchPatients(event) {
        const query = event.target.value.trim()
        
        if (query.length < 2) {
            this.loadPatientsList()
            this.clearSearchResults()
            return
        }
        
        try {
            const allPatients = await this.getAllPatients()
            const filteredPatients = this.filterPatients(allPatients, query)
            
            this.displayPatientsList(filteredPatients)
            this.displaySearchResults(filteredPatients, query)
            
            this.log(`🔍 Búsqueda: "${query}" - ${filteredPatients.length} resultados`)
            
        } catch (error) {
            this.handleError("Error en búsqueda", error)
        }
    }

    // 🔄 Refresh completo de datos
    async refresh() {
        this.allPatientsCache = null
        await this.loadPatientsList()
        this.log("🔄 Datos actualizados desde API Platform")
    }

    // 🧹 Limpiar selección e información
    clearInfo() {
        if (this.hasInfoTarget) {
            this.infoTarget.style.display = "none"
        }
        
        this.clearVisualSelection()
        this.selectedPatientId = null
        this.patientIdValue = ""
        
        this.log("🧹 Información limpiada")
    }

    // 📡 Método base para llamadas a API Platform
    async fetchFromAPI(endpoint, options = {}) {
        const defaultHeaders = {
            'Accept': 'application/json',
            'X-Tenant-Context': this.tenantValue,
            'X-Requested-With': 'XMLHttpRequest'
        }
        
        const config = {
            method: 'GET',
            headers: { ...defaultHeaders, ...options.headers },
            ...options
        }
        
        const response = await fetch(endpoint, config)
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`)
        }
        
        return response
    }

    // 🏪 Obtener todos los pacientes (con cache)
    async getAllPatients() {
        if (this.cacheEnabledValue && this.allPatientsCache) {
            return this.allPatientsCache
        }
        
        const response = await this.fetchFromAPI(this.apiUrlValue)
        const patients = await response.json()
        
        if (this.cacheEnabledValue) {
            this.allPatientsCache = patients
        }
        
        return patients
    }

    // 🔍 Filtrar pacientes por query
    filterPatients(patients, query) {
        const lowerQuery = query.toLowerCase()
        
        return patients.filter(patient => 
            patient.name?.toLowerCase().includes(lowerQuery) ||
            patient.email?.toLowerCase().includes(lowerQuery) ||
            patient.cedula?.includes(query) ||
            patient.id?.toLowerCase().includes(lowerQuery)
        )
    }

    // 🎯 Mostrar resultados de búsqueda
    displaySearchResults(patients, query) {
        if (!this.hasSearchResultsTarget) return
        
        this.searchResultsTarget.innerHTML = `
            <div class="mb-3 p-2 bg-blue-50 border border-blue-200 rounded">
                <p class="text-blue-700 text-sm">
                    🔍 <strong>${patients.length}</strong> resultado${patients.length !== 1 ? 's' : ''} 
                    para "<strong>${query}</strong>"
                </p>
            </div>
        `
    }

    // 🧹 Limpiar resultados de búsqueda
    clearSearchResults() {
        if (this.hasSearchResultsTarget) {
            this.searchResultsTarget.innerHTML = ''
        }
    }

    // 🎨 Actualizar selección visual
    updateVisualSelection(selectedCard) {
        // Limpiar selecciones anteriores
        this.clearVisualSelection()
        
        // Marcar nueva selección
        selectedCard.classList.add('bg-blue-100', 'border-blue-300')
    }

    // 🧹 Limpiar selección visual
    clearVisualSelection() {
        document.querySelectorAll('.patient-card').forEach(card => {
            card.classList.remove('bg-blue-100', 'border-blue-300')
        })
    }

    // 📋 UI Helper Methods
    showLoading(message = "Cargando...") {
        if (this.hasLoadingTarget) {
            this.loadingTarget.style.display = "block"
            this.loadingTarget.querySelector('p').textContent = message
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

    // 🛠️ Utility Methods
    updateTarget(targetName, value) {
        const target = this[`${targetName}Target`]
        if (target) {
            target.textContent = value || `Sin ${targetName.toLowerCase()}`
        }
    }

    formatGender(gender) {
        const genderMap = {
            'M': 'Masculino',
            'F': 'Femenino',
            'O': 'Otro'
        }
        return genderMap[gender] || 'No especificado'
    }

    formatDate(dateString) {
        if (!dateString) return 'Sin fecha'
        return new Date(dateString).toLocaleDateString('es-CL')
    }

    formatDateTime(dateString) {
        if (!dateString) return 'Sin fecha'
        return new Date(dateString).toLocaleString('es-CL')
    }

    formatArray(array, emptyMessage = 'Sin datos') {
        if (!array || array.length === 0) return emptyMessage
        return array.join(', ')
    }

    calculateAge(birthDate) {
        if (!birthDate) return null
        const today = new Date()
        const birth = new Date(birthDate)
        const age = today.getFullYear() - birth.getFullYear()
        const monthDiff = today.getMonth() - birth.getMonth()
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            return age - 1
        }
        return age
    }

    getTenantDisplayName(tenant) {
        const tenantNames = {
            'melisahospital': 'Hospital Melisa',
            'melisalacolina': 'Clínica La Colina',
            'melisawiclinic': 'Wi Clinic'
        }
        return tenantNames[tenant] || tenant
    }

    createEmptyState() {
        return `
            <div class="text-center py-8 text-gray-500">
                <div class="text-4xl mb-2">🏥</div>
                <p class="font-medium">No hay pacientes</p>
                <p class="text-sm">No se encontraron pacientes en este tenant</p>
            </div>
        `
    }

    setupGlobalListeners() {
        // Listener para teclas de acceso rápido
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault()
                this.refresh()
            }
        })
    }

    handleError(message, error) {
        console.error(`❌ ${message}:`, error)
        this.showError(`${message}: ${error.message}`)
    }

    log(message) {
        if (this.debugModeValue) {
            console.log(`🏥 [API Platform Patient] ${message}`)
        }
    }

    // 🔄 Lifecycle callbacks
    patientIdValueChanged() {
        if (this.patientIdValue) {
            this.log(`🆔 Patient ID cambió a: ${this.patientIdValue}`)
        }
    }

    disconnect() {
        this.log("❌ Controlador desconectado")
    }
}