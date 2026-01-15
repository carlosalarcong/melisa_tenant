import { Controller } from '@hotwired/stimulus';

/**
 * Dashboard Moderno Controller
 * 
 * Maneja la interactividad del dashboard:
 * - Filtrado de módulos
 * - Búsqueda global
 * - Animaciones
 * - Actualización de métricas en tiempo real (Turbo)
 */
export default class extends Controller {
    static targets = [
        'module',
        'searchInput',
        'metricValue'
    ];

    static values = {
        refreshInterval: { type: Number, default: 30000 }, // 30 segundos
        autoRefresh: { type: Boolean, default: false }
    };

    connect() {
        console.log('🚀 Dashboard Controller conectado');
        
        // Inicializar tooltips
        this.initializeTooltips();
        
        // Auto-refresh de métricas si está habilitado
        if (this.autoRefreshValue) {
            this.startAutoRefresh();
        }

        // Atajos de teclado
        this.setupKeyboardShortcuts();
    }

    disconnect() {
        if (this.refreshTimer) {
            clearInterval(this.refreshTimer);
        }
    }

    /**
     * Filtrar módulos por categoría
     */
    filterModules(event) {
        const category = event.currentTarget.dataset.category;
        
        this.moduleTargets.forEach(module => {
            const moduleCategory = module.dataset.category;
            
            if (category === 'all' || moduleCategory === category) {
                module.style.display = 'block';
                module.classList.add('animate-in');
            } else {
                module.style.display = 'none';
            }
        });
    }

    /**
     * Filtrar solo módulos destacados
     */
    filterFeatured() {
        this.moduleTargets.forEach(module => {
            const isFeatured = module.dataset.featured === 'true';
            module.style.display = isFeatured ? 'block' : 'none';
        });
    }

    /**
     * Buscar módulos en tiempo real
     */
    searchModules(event) {
        const query = event.target.value.toLowerCase();
        
        this.moduleTargets.forEach(module => {
            const moduleName = module.querySelector('.module-name').textContent.toLowerCase();
            const moduleDesc = module.querySelector('.module-description').textContent.toLowerCase();
            
            const matches = moduleName.includes(query) || moduleDesc.includes(query);
            module.style.display = matches || query === '' ? 'block' : 'none';
        });
    }

    /**
     * Abrir búsqueda global
     */
    openGlobalSearch(event) {
        if (event) event.preventDefault();
        
        const searchModal = document.getElementById('searchModal');
        if (searchModal) {
            const modal = new bootstrap.Modal(searchModal);
            modal.show();
            
            // Focus en el input después de que se abra
            setTimeout(() => {
                const input = searchModal.querySelector('input[type="text"]');
                if (input) input.focus();
            }, 300);
        }
    }

    /**
     * Marcar módulo como favorito
     */
    toggleFavorite(event) {
        event.preventDefault();
        const moduleCard = event.currentTarget.closest('.module-card');
        const moduleId = moduleCard.dataset.moduleId;
        
        // Aquí harías una petición al backend para guardar el favorito
        console.log('Toggle favorite:', moduleId);
        
        // Feedback visual
        const icon = event.currentTarget.querySelector('i');
        icon.classList.toggle('fas');
        icon.classList.toggle('far');
        
        this.showToast('Favorito actualizado', 'success');
    }

    /**
     * Refrescar métricas
     */
    async refreshMetrics() {
        try {
            const response = await fetch('/api/dashboard/metrics', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.updateMetricsDisplay(data);
            }
        } catch (error) {
            console.error('Error refrescando métricas:', error);
        }
    }

    /**
     * Actualizar valores de métricas en el DOM
     */
    updateMetricsDisplay(data) {
        this.metricValueTargets.forEach(target => {
            const metricType = target.dataset.metricType;
            const value = data[metricType];
            
            if (value !== undefined) {
                // Animación de cambio de valor
                target.classList.add('updating');
                setTimeout(() => {
                    target.textContent = value;
                    target.classList.remove('updating');
                }, 300);
            }
        });
    }

    /**
     * Iniciar auto-refresh de métricas
     */
    startAutoRefresh() {
        this.refreshTimer = setInterval(() => {
            this.refreshMetrics();
        }, this.refreshIntervalValue);
    }

    /**
     * Configurar atajos de teclado
     */
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + K para búsqueda global
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                this.openGlobalSearch();
            }
            
            // Ctrl/Cmd + R para refrescar métricas
            if ((e.ctrlKey || e.metaKey) && e.key === 'r' && e.shiftKey) {
                e.preventDefault();
                this.refreshMetrics();
                this.showToast('Métricas actualizadas', 'info');
            }
        });
    }

    /**
     * Inicializar tooltips de Bootstrap
     */
    initializeTooltips() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }

    /**
     * Mostrar notificación toast
     */
    showToast(message, type = 'info') {
        // Crear toast si no existe
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }

        const toastHTML = `
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        const toastElement = toastContainer.lastElementChild;
        const toast = new bootstrap.Toast(toastElement);
        toast.show();

        // Eliminar del DOM después de ocultarse
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    /**
     * Animación de entrada para tarjetas
     */
    animateCards() {
        const cards = document.querySelectorAll('.metric-card, .module-card');
        
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease-out';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }
}
