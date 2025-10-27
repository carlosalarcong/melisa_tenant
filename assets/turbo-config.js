// Configuración global de Turbo para optimizar el rendimiento y evitar warnings

// Configuración de Turbo para reducir conflictos con import maps
import { Turbo } from '@hotwired/turbo';

// Configurar Turbo para que sea más selectivo sobre qué elementos recarga
Turbo.config.action = 'replace';

// Agregar listeners específicos para optimizar la carga
document.addEventListener('turbo:before-render', function(event) {
    // Preservar elementos permanentes más específicamente
    const importMaps = document.querySelectorAll('script[type="importmap"]');
    importMaps.forEach(map => {
        if (!map.hasAttribute('data-turbo-permanent')) {
            map.setAttribute('data-turbo-permanent', '');
        }
    });
    
    // Preservar stylesheets específicos del tenant
    const stylesheets = document.querySelectorAll('link[rel="stylesheet"]');
    stylesheets.forEach(link => {
        if (link.href.includes('bootstrap') || link.href.includes('fontawesome')) {
            if (!link.hasAttribute('data-turbo-permanent')) {
                link.setAttribute('data-turbo-permanent', '');
            }
        }
    });
});

// Optimizar carga de páginas
document.addEventListener('turbo:load', function() {
    // Reinicializar tooltips de Bootstrap después de navegación Turbo
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Reinicializar animaciones de fade-in
    const cards = document.querySelectorAll('.card:not(.fade-in-applied)');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('fade-in', 'fade-in-applied');
    });
});

// Manejar errores de carga más elegantemente
document.addEventListener('turbo:fetch-request-error', function(event) {
    console.warn('Error de navegación Turbo:', event.detail.error);
});

// Configuración para desarrollo - silenciar warnings molestos pero no críticos
if (process.env.NODE_ENV === 'development') {
    const originalConsoleWarn = console.warn;
    console.warn = function(...args) {
        const message = args.join(' ');
        
        // Lista de warnings que podemos silenciar de forma segura
        const silencePatterns = [
            /import map rule for specifier.*was removed.*conflicted/i,
            /An import map rule for specifier.*was removed/i
        ];
        
        const shouldSilence = silencePatterns.some(pattern => pattern.test(message));
        
        if (!shouldSilence) {
            originalConsoleWarn.apply(console, args);
        }
    };
}

export default Turbo;