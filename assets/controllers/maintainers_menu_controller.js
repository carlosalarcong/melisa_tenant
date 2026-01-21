import { Controller } from '@hotwired/stimulus';

/**
 * Maintainers Menu Controller
 * 
 * Maneja el comportamiento del menú desplegable de Mantenedores:
 * - Toggle expand/collapse
 * - Rotación del ícono chevron
 * - Persistencia del estado en localStorage
 */
export default class extends Controller {
    static targets = ['submenu', 'icon'];

    connect() {
        // Restaurar estado previo del menú
        const isExpanded = localStorage.getItem('maintainersMenuExpanded') === 'true';
        if (isExpanded) {
            this.expand();
        }
    }

    toggle(event) {
        event.preventDefault();
        
        const submenu = this.submenuTarget;
        const icon = this.iconTarget;
        
        if (submenu.classList.contains('show')) {
            this.collapse();
        } else {
            this.expand();
        }
    }

    expand() {
        const submenu = this.submenuTarget;
        const icon = this.iconTarget;
        
        // Usar Bootstrap collapse API
        const bsCollapse = new bootstrap.Collapse(submenu, {
            toggle: true
        });
        
        // Rotar ícono
        icon.style.transform = 'rotate(180deg)';
        
        // Guardar estado
        localStorage.setItem('maintainersMenuExpanded', 'true');
    }

    collapse() {
        const submenu = this.submenuTarget;
        const icon = this.iconTarget;
        
        // Usar Bootstrap collapse API
        const bsCollapse = bootstrap.Collapse.getInstance(submenu);
        if (bsCollapse) {
            bsCollapse.hide();
        }
        
        // Restaurar ícono
        icon.style.transform = 'rotate(0deg)';
        
        // Guardar estado
        localStorage.setItem('maintainersMenuExpanded', 'false');
    }
}
