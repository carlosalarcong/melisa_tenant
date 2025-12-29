import { Controller } from '@hotwired/stimulus';

/**
 * Controlador que muestra badges de las opciones seleccionadas en un select múltiple
 * Permite visualizar rápidamente lo seleccionado sin hacer scroll
 * Permite remover items haciendo click en el badge
 */
export default class extends Controller {
    static targets = ['select', 'badgeContainer', 'emptyMessage'];
    static values = { label: String };

    connect() {
        // Actualizar badges al cargar si ya hay selecciones
        this.updateBadges();
    }

    updateBadges() {
        const selectedOptions = Array.from(this.selectTarget.selectedOptions);
        
        // Limpiar contenedor
        this.badgeContainerTarget.innerHTML = '';

        if (selectedOptions.length === 0) {
            // Mostrar mensaje de "ninguna seleccionada"
            const emptyMsg = document.createElement('small');
            emptyMsg.className = 'text-muted';
            emptyMsg.textContent = 'Ninguna seleccionada';
            this.badgeContainerTarget.appendChild(emptyMsg);
            return;
        }

        // Crear badge por cada opción seleccionada
        selectedOptions.forEach(option => {
            const badge = this.createBadge(option.text, option.value);
            this.badgeContainerTarget.appendChild(badge);
        });
    }

    createBadge(text, value) {
        const badge = document.createElement('span');
        badge.className = 'badge bg-primary d-inline-flex align-items-center gap-1';
        badge.style.cursor = 'pointer';
        badge.dataset.value = value;
        
        // Texto del badge
        const textSpan = document.createElement('span');
        textSpan.textContent = text;
        badge.appendChild(textSpan);
        
        // Botón de remover (X)
        const removeBtn = document.createElement('i');
        removeBtn.className = 'ri-close-line';
        removeBtn.style.fontSize = '14px';
        badge.appendChild(removeBtn);
        
        // Evento para remover
        badge.addEventListener('click', (e) => {
            e.preventDefault();
            this.removeSelection(value);
        });
        
        return badge;
    }

    removeSelection(value) {
        // Deseleccionar la opción en el select
        const option = this.selectTarget.querySelector(`option[value="${value}"]`);
        if (option) {
            option.selected = false;
            
            // Disparar evento change para que otros controladores se enteren
            this.selectTarget.dispatchEvent(new Event('change', { bubbles: true }));
            
            // Actualizar badges
            this.updateBadges();
        }
    }
}
