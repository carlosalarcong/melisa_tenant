import ApiPatientController from "../default/api_patient_controller.js"

/**
 * Controlador especializado para Wi Clinic
 * 
 * Extiende el controlador base con funcionalidades tecnológicas:
 * - Integración con wearables y sensores IoT
 * - Datos de telemedicina
 * - Métricas de salud digital
 */
export default class extends ApiPatientController {
    
    // Targets adicionales específicos de Wi Clinic
    static targets = [
        ...ApiPatientController.targets,
        "techDevices", "telemetryData", "aiDiagnosis",
        "vrSessions", "blockchainHash", "iotMetrics"
    ]

    // Values específicos de Wi Clinic
    static values = {
        ...ApiPatientController.values,
        enableTechFeatures: { type: Boolean, default: true },
        showTelemetry: { type: Boolean, default: true },
        enableVR: { type: Boolean, default: false }
    }

    connect() {
        super.connect()
        this.log("💻 Wi Clinic Tech Controller conectado")
        
        // Configuración específica tecnológica
        this.setupTechFeatures()
        this.startTelemetrySimulation()
    }

    // 🎨 Override: Crear card con indicadores tecnológicos
    createPatientCard(patient) {
        const card = super.createPatientCard(patient)
        
        // Agregar indicadores tecnológicos
        if (patient.tenant === 'melisawiclinic') {
            const techIndicators = this.createTechIndicators(patient)
            if (techIndicators) {
                card.querySelector('.text-right').appendChild(techIndicators)
            }
        }
        
        return card
    }

    // 💻 Crear indicadores tecnológicos
    createTechIndicators(patient) {
        const indicators = document.createElement('div')
        indicators.className = 'mt-2 space-y-1'
        
        const techFeatures = this.detectTechFeatures(patient)
        
        indicators.innerHTML = techFeatures.map(feature => {
            const icons = {
                'telemedicine': '📡',
                'wearables': '⌚',
                'iot': '🌐',
                'ai': '🤖',
                'vr': '🥽',
                'blockchain': '🔗'
            }
            
            return `
                <div class="flex items-center text-xs text-purple-600">
                    <span class="mr-1">${icons[feature.type] || '💻'}</span>
                    <span>${feature.label}</span>
                </div>
            `
        }).join('')
        
        return indicators
    }

    // 🔍 Detectar características tecnológicas del paciente
    detectTechFeatures(patient) {
        const features = []
        const name = patient.name.toLowerCase()
        
        if (name.includes('telemedicina')) {
            features.push({ type: 'telemedicine', label: 'Telemedicina' })
        }
        if (name.includes('wearables') || name.includes('sensor')) {
            features.push({ type: 'wearables', label: 'Wearables' })
        }
        if (name.includes('iot')) {
            features.push({ type: 'iot', label: 'IoT' })
        }
        if (name.includes('ai') || name.includes('inteligencia')) {
            features.push({ type: 'ai', label: 'IA' })
        }
        if (name.includes('vr') || name.includes('virtual')) {
            features.push({ type: 'vr', label: 'VR' })
        }
        if (name.includes('blockchain')) {
            features.push({ type: 'blockchain', label: 'Blockchain' })
        }
        
        return features
    }

    // 📊 Override: Mostrar información con datos tecnológicos
    displayPatientInfo(patient) {
        super.displayPatientInfo(patient)
        
        if (this.enableTechFeaturesValue) {
            this.displayTechInfo(patient)
        }
        
        if (this.showTelemetryValue) {
            this.startPatientTelemetry(patient)
        }
    }

    // 💻 Mostrar información tecnológica
    displayTechInfo(patient) {
        const techFeatures = this.detectTechFeatures(patient)
        
        // Dispositivos tecnológicos
        const devices = this.generateTechDevices(patient)
        this.updateTarget('techDevices', devices.join(', '))
        
        // Hash blockchain simulado
        if (patient.name.includes('blockchain')) {
            const hash = this.generateBlockchainHash(patient.id)
            this.updateTarget('blockchainHash', hash)
        }
        
        // Diagnóstico de IA simulado
        if (patient.name.includes('ai')) {
            const aiDiagnosis = this.generateAIDiagnosis(patient)
            this.updateTarget('aiDiagnosis', aiDiagnosis)
        }
    }

    // 📱 Generar dispositivos tecnológicos
    generateTechDevices(patient) {
        const devices = []
        const name = patient.name.toLowerCase()
        
        if (name.includes('wearables')) {
            devices.push('Apple Watch Series 9', 'Fitbit Sense 2')
        }
        if (name.includes('sensor')) {
            devices.push('Sensor glucosa Dexcom G7', 'Monitor presión Omron')
        }
        if (name.includes('vr')) {
            devices.push('Meta Quest 3', 'HTC Vive Pro')
        }
        if (name.includes('iot')) {
            devices.push('ESP32 Health Monitor', 'Arduino Nano IoT')
        }
        
        return devices.length > 0 ? devices : ['Smartphone básico']
    }

    // 🔗 Generar hash blockchain
    generateBlockchainHash(patientId) {
        // Simular hash SHA-256
        const hash = btoa(patientId + Date.now()).replace(/[^a-f0-9]/gi, '').toLowerCase()
        return hash.substring(0, 16) + '...'
    }

    // 🤖 Generar diagnóstico de IA
    generateAIDiagnosis(patient) {
        const diagnoses = [
            'Riesgo cardiovascular: Bajo (12%)',
            'Indicadores metabólicos: Normales',
            'Patrón de sueño: Óptimo (8.2h promedio)',
            'Nivel de actividad: Moderado-Alto'
        ]
        
        const index = patient.id.charCodeAt(patient.id.length - 1) % diagnoses.length
        return diagnoses[index]
    }

    // 📡 Iniciar telemetría del paciente
    startPatientTelemetry(patient) {
        if (!this.hasTelemetryDataTarget) return
        
        // Simular datos en tiempo real
        this.telemetryInterval = setInterval(() => {
            const data = this.generateTelemetryData()
            this.updateTelemetryDisplay(data)
        }, 3000)
    }

    // 📊 Generar datos de telemetría simulados
    generateTelemetryData() {
        return {
            heartRate: Math.floor(Math.random() * 40) + 60, // 60-100 bpm
            steps: Math.floor(Math.random() * 1000) + 8000, // 8000-9000 steps
            bloodOxygen: Math.floor(Math.random() * 5) + 95, // 95-100%
            temperature: (Math.random() * 2 + 36).toFixed(1), // 36.0-38.0°C
            timestamp: new Date().toLocaleTimeString()
        }
    }

    // 📈 Actualizar display de telemetría
    updateTelemetryDisplay(data) {
        if (this.hasTelemetryDataTarget) {
            this.telemetryDataTarget.innerHTML = `
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div class="bg-purple-50 p-2 rounded">
                        <span class="text-purple-600">❤️ ${data.heartRate} bpm</span>
                    </div>
                    <div class="bg-blue-50 p-2 rounded">
                        <span class="text-blue-600">🚶 ${data.steps} pasos</span>
                    </div>
                    <div class="bg-green-50 p-2 rounded">
                        <span class="text-green-600">🫁 ${data.bloodOxygen}% O2</span>
                    </div>
                    <div class="bg-red-50 p-2 rounded">
                        <span class="text-red-600">🌡️ ${data.temperature}°C</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Última actualización: ${data.timestamp}
                </p>
            `
        }
    }

    // 🔧 Configurar características tecnológicas
    setupTechFeatures() {
        // Configurar colores específicos de Wi Clinic
        document.documentElement.style.setProperty('--tech-primary', '#7c3aed')
        document.documentElement.style.setProperty('--tech-secondary', '#f3e8ff')
        
        // Agregar estilos tech
        this.addTechStyles()
    }

    // 🎨 Agregar estilos CSS tecnológicos
    addTechStyles() {
        const style = document.createElement('style')
        style.textContent = `
            .wiclinic-patient-card {
                border-left: 4px solid #7c3aed;
                background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
            }
            .tech-indicator {
                animation: pulse 2s infinite;
            }
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.7; }
            }
            .telemetry-data {
                font-family: 'Courier New', monospace;
            }
        `
        document.head.appendChild(style)
    }

    // 📡 Iniciar simulación de telemetría
    startTelemetrySimulation() {
        // Simular conexión con dispositivos IoT
        console.log('💻 Iniciando simulación de telemetría Wi Clinic...')
        
        // Simular eventos de conectividad
        setTimeout(() => {
            this.log('📡 Dispositivos IoT conectados')
        }, 2000)
    }

    // 🧹 Limpiar recursos al cambiar paciente
    clearInfo() {
        super.clearInfo()
        
        // Limpiar intervalos de telemetría
        if (this.telemetryInterval) {
            clearInterval(this.telemetryInterval)
            this.telemetryInterval = null
        }
    }

    // 🔄 Override: Refresh con actualización tech
    async refresh() {
        await super.refresh()
        
        // Reiniciar telemetría si hay paciente seleccionado
        if (this.selectedPatientId) {
            const patient = await this.getPatientById(this.selectedPatientId)
            if (patient) {
                this.startPatientTelemetry(patient)
            }
        }
    }

    // 👤 Obtener paciente por ID
    async getPatientById(patientId) {
        try {
            const allPatients = await this.getAllPatients()
            return allPatients.find(p => p.id === patientId)
        } catch (error) {
            this.handleError("Error obteniendo paciente", error)
            return null
        }
    }

    disconnect() {
        super.disconnect()
        
        // Limpiar intervalos
        if (this.telemetryInterval) {
            clearInterval(this.telemetryInterval)
        }
    }

    log(message) {
        if (this.debugModeValue) {
            console.log(`💻 [Wi Clinic] ${message}`)
        }
    }
}