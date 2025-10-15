import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

// Importar Dynamic Controller Loader
import DynamicControllerLoader from './controllers/dynamic_loader.js';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// Configurar Dynamic Controller Loading cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', async () => {
    // Obtener la aplicación Stimulus del bootstrap
    const { Application } = await import('@hotwired/stimulus');
    
    // Buscar la instancia de la aplicación Stimulus
    if (window.Stimulus) {
        console.log('🎮 Configurando Dynamic Controller Loader...');
        
        try {
            // Auto-registrar controllers encontrados en el DOM
            await DynamicControllerLoader.autoRegisterControllers(window.Stimulus);
            
            console.log('🎮 Dynamic Controller Loader configurado exitosamente');
            console.log('🎮 Debug info:', DynamicControllerLoader.getDebugInfo());
            
        } catch (error) {
            console.error('🎮 Error configurando Dynamic Controller Loader:', error);
        }
    } else {
        console.warn('🎮 Stimulus application no encontrada');
    }
});
