import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

// Importar configuración optimizada de Turbo
import './turbo-config.js';

// Importar SweetAlert2
import Swal from 'sweetalert2';

// Hacer SweetAlert2 disponible globalmente
window.Swal = Swal;

// Importar Dynamic Controller Loader
import DynamicControllerLoader from './controllers/dynamic_loader.js';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// Función helper para esperar que Stimulus esté disponible
function waitForStimulus(maxAttempts = 10, interval = 200) {
    return new Promise((resolve, reject) => {
        let attempts = 0;
        
        const checkStimulus = () => {
            attempts++;
            
            if (window.Stimulus) {
                console.log(`🎮 Stimulus encontrado después de ${attempts} intentos`);
                resolve(window.Stimulus);
            } else if (attempts >= maxAttempts) {
                console.log('🎮 Stimulus no se inicializó, pero esto es normal para páginas sin controllers dinámicos');
                resolve(null);
            } else {
                setTimeout(checkStimulus, interval);
            }
        };
        
        checkStimulus();
    });
}

// Configurar Dynamic Controller Loading cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', async () => {
    console.log('🎮 DOM cargado, esperando Stimulus...');
    
    const stimulus = await waitForStimulus();
    
    if (stimulus) {
        console.log('🎮 Configurando Dynamic Controller Loader...');
        
        try {
            // Auto-registrar controllers encontrados en el DOM
            await DynamicControllerLoader.autoRegisterControllers(stimulus);
            
            console.log('🎮 Dynamic Controller Loader configurado exitosamente');
            console.log('🎮 Debug info:', DynamicControllerLoader.getDebugInfo());
            
        } catch (error) {
            console.error('🎮 Error configurando Dynamic Controller Loader:', error);
        }
    }
});

// También configurar en el evento turbo:load para navegaciones SPA
document.addEventListener('turbo:load', async () => {
    const stimulus = await waitForStimulus(5, 100); // Menos intentos en navegaciones turbo
    
    if (stimulus) {
        try {
            await DynamicControllerLoader.autoRegisterControllers(stimulus);
            console.log('🎮 Dynamic Controller Loader reconfigurado después de navegación Turbo');
        } catch (error) {
            console.error('🎮 Error reconfigurando Dynamic Controller Loader después de Turbo:', error);
        }
    }
    
    // Reinicializar componentes de Bootstrap
    initializeBootstrapComponents();
});

// Reinicializar Bootstrap después de cargar Turbo Frames
document.addEventListener('turbo:frame-load', () => {
    initializeBootstrapComponents();
});

// Función para inicializar componentes de Bootstrap
function initializeBootstrapComponents() {
    // Reinicializar todos los dropdowns
    const dropdownElementList = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    dropdownElementList.forEach((dropdownToggleEl) => {
        // Verificar si ya existe una instancia
        const existingInstance = bootstrap.Dropdown.getInstance(dropdownToggleEl);
        if (!existingInstance) {
            new bootstrap.Dropdown(dropdownToggleEl);
        }
    });
    
    // Reinicializar tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach((tooltipTriggerEl) => {
        const existingInstance = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
        if (!existingInstance) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        }
    });
    
    // Reinicializar popovers
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    popoverTriggerList.forEach((popoverTriggerEl) => {
        const existingInstance = bootstrap.Popover.getInstance(popoverTriggerEl);
        if (!existingInstance) {
            new bootstrap.Popover(popoverTriggerEl);
        }
    });
}

// Configuración adicional para reducir warnings de import map
if (typeof document !== 'undefined') {
    // Silenciar warnings específicos en desarrollo
    const originalWarn = console.warn;
    console.warn = function(...args) {
        const message = args.join(' ');
        
        // Patterns de warnings que podemos silenciar de forma segura
        const silencePatterns = [
            /import map rule for specifier.*was removed.*conflicted/i,
            /An import map rule for specifier.*was removed/i
        ];
        
        const shouldSilence = silencePatterns.some(pattern => pattern.test(message));
        
        if (!shouldSilence) {
            originalWarn.apply(console, args);
        }
    };
}
