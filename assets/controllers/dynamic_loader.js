/**
 * Dynamic Controller Loader para Stimulus
 * 
 * Sistema que permite cargar controllers con fallback:
 * 1. Busca en subdomain específico (ej: melisalacolina)
 * 2. Si no existe, busca en default
 * 
 * Uso en HTML:
 * data-controller="internal--patient" -> busca internal/[subdomain]/patient_controller.js
 * data-controller="apiplatform--api-patient" -> busca apiplatform/[subdomain]/api_patient_controller.js
 */

class DynamicControllerLoader {
    constructor() {
        this.currentSubdomain = this.detectSubdomain()
        this.loadedControllers = new Map()
        
        console.log(`🎮 [Dynamic Loader] Subdomain detectado: ${this.currentSubdomain}`)
    }

    detectSubdomain() {
        const hostname = window.location.hostname
        
        // Extraer subdomain (ej: melisalacolina.localhost -> melisalacolina)
        const parts = hostname.split('.')
        if (parts.length > 1 && parts[0] !== 'www') {
            return parts[0]
        }
        
        // Fallback para desarrollo local
        if (hostname === 'localhost' || hostname.startsWith('127.0.0.1')) {
            return 'melisahospital' // default para desarrollo
        }
        
        return 'melisahospital' // default
    }

    /**
     * Cargar controller con fallback
     * @param {string} controllerType - 'internal' o 'apiplatform'
     * @param {string} controllerName - nombre del controller (ej: 'patient', 'api-patient')
     * @returns {Promise<Controller>} - Clase del controller
     */
    async loadController(controllerType, controllerName) {
        const cacheKey = `${controllerType}--${controllerName}`
        
        // Verificar cache
        if (this.loadedControllers.has(cacheKey)) {
            return this.loadedControllers.get(cacheKey)
        }

        let controller = null
        
        try {
            // 1. Intentar cargar desde subdomain específico
            controller = await this.tryLoadFromSubdomain(controllerType, controllerName)
            
            if (controller) {
                console.log(`🎮 [Dynamic Loader] ✅ Cargado desde ${this.currentSubdomain}: ${controllerType}/${controllerName}`)
            }
        } catch (error) {
            console.log(`🎮 [Dynamic Loader] ⚠️ No encontrado en ${this.currentSubdomain}: ${controllerType}/${controllerName}`)
        }

        if (!controller) {
            try {
                // 2. Fallback: cargar desde default
                controller = await this.tryLoadFromDefault(controllerType, controllerName)
                
                if (controller) {
                    console.log(`🎮 [Dynamic Loader] ✅ Cargado desde default: ${controllerType}/${controllerName}`)
                }
            } catch (error) {
                console.error(`🎮 [Dynamic Loader] ❌ No encontrado en default: ${controllerType}/${controllerName}`, error)
                throw new Error(`Controller no encontrado: ${controllerType}--${controllerName}`)
            }
        }

        // Guardar en cache
        if (controller) {
            this.loadedControllers.set(cacheKey, controller)
        }

        return controller
    }

    async tryLoadFromSubdomain(controllerType, controllerName) {
        const path = this.buildControllerPath(controllerType, this.currentSubdomain, controllerName)
        return await this.importController(path)
    }

    async tryLoadFromDefault(controllerType, controllerName) {
        const path = this.buildControllerPath(controllerType, 'default', controllerName)
        return await this.importController(path)
    }

    buildControllerPath(controllerType, subdomain, controllerName) {
        // Convertir nombres con guiones a underscore para archivos
        const fileName = controllerName.replace(/-/g, '_') + '_controller.js'
        return `./controllers/${controllerType}/${subdomain}/${fileName}`
    }

    async importController(path) {
        try {
            const module = await import(path)
            return module.default
        } catch (error) {
            // Re-lanzar error para que el caller pueda manejarlo
            throw error
        }
    }

    /**
     * Registrar controller en Stimulus Application
     * @param {object} application - Instancia de Stimulus Application
     * @param {string} identifier - Identificador del controller (ej: 'internal--patient')
     */
    async registerController(application, identifier) {
        try {
            // Parsear identificador: 'internal--patient' -> type: 'internal', name: 'patient'
            const [controllerType, controllerName] = this.parseIdentifier(identifier)
            
            // Cargar controller con fallback
            const controllerClass = await this.loadController(controllerType, controllerName)
            
            // Registrar en Stimulus
            application.register(identifier, controllerClass)
            
            console.log(`🎮 [Dynamic Loader] ✅ Registrado: ${identifier}`)
            
        } catch (error) {
            console.error(`🎮 [Dynamic Loader] ❌ Error registrando ${identifier}:`, error)
            throw error
        }
    }

    parseIdentifier(identifier) {
        // Identificadores esperados:
        // 'internal--patient' -> ['internal', 'patient']
        // 'apiplatform--api-patient' -> ['apiplatform', 'api-patient']
        
        const parts = identifier.split('--')
        if (parts.length !== 2) {
            throw new Error(`Identificador inválido: ${identifier}. Formato esperado: 'type--name'`)
        }
        
        return parts
    }

    /**
     * Auto-registrar todos los controllers encontrados en el DOM
     * @param {object} application - Instancia de Stimulus Application
     */
    async autoRegisterControllers(application) {
        // Buscar todos los elementos con data-controller en el DOM
        const elements = document.querySelectorAll('[data-controller]')
        const identifiers = new Set()

        elements.forEach(element => {
            const controllers = element.getAttribute('data-controller').split(' ')
            controllers.forEach(controller => {
                // Solo procesar controllers con nuestro formato
                if (controller.includes('--')) {
                    identifiers.add(controller)
                }
            })
        })

        console.log(`🎮 [Dynamic Loader] Controllers encontrados en DOM:`, Array.from(identifiers))

        // Registrar cada controller encontrado
        for (const identifier of identifiers) {
            try {
                await this.registerController(application, identifier)
            } catch (error) {
                console.warn(`🎮 [Dynamic Loader] ⚠️ No se pudo registrar ${identifier}:`, error.message)
            }
        }
    }

    /**
     * Obtener información de debug
     */
    getDebugInfo() {
        return {
            subdomain: this.currentSubdomain,
            loadedControllers: Array.from(this.loadedControllers.keys()),
            hostname: window.location.hostname
        }
    }
}

// Crear instancia global
window.DynamicControllerLoader = new DynamicControllerLoader()

// Exportar para uso en módulos
export default window.DynamicControllerLoader