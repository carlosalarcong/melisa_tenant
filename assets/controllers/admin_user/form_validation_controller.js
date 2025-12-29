import { Controller } from '@hotwired/stimulus';

/**
 * Form Validation Controller
 * 
 * Maneja la validación de formularios con Bootstrap 5.
 * Detecta campos inválidos, cambia a la pestaña correcta y muestra notificaciones.
 * 
 * Targets: ninguno (trabaja directamente con el elemento del controller)
 * 
 * Uso:
 * <form data-controller="admin-user--form-validation" class="needs-validation" novalidate>
 *   ...
 * </form>
 */
export default class extends Controller {
    connect() {
        console.log('Form validation controller connected');
        
        // Agregar listener al submit del formulario
        this.element.addEventListener('submit', this.handleSubmit.bind(this));
    }

    disconnect() {
        this.element.removeEventListener('submit', this.handleSubmit.bind(this));
    }

    handleSubmit(event) {
        if (!this.element.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            
            // Encontrar el primer campo inválido y mostrar su pestaña
            this.showFirstInvalidFieldTab();
            
            // Mostrar notificación toast
            this.showToastNotification();
        }
        
        // Agregar clase de validación para mostrar feedback visual
        this.element.classList.add('was-validated');
    }

    showFirstInvalidFieldTab() {
        const firstInvalid = this.element.querySelector('.is-invalid, :invalid');
        
        if (!firstInvalid) return;

        const tabPane = firstInvalid.closest('.tab-pane');
        
        if (!tabPane) return;

        const tabId = tabPane.getAttribute('id');
        const tabLink = document.querySelector(`[href="#${tabId}"]`);
        
        if (!tabLink) return;

        // Cambiar a la pestaña que contiene el campo inválido
        const tab = new bootstrap.Tab(tabLink);
        tab.show();
        
        // Scroll al campo inválido después de cambiar de pestaña
        setTimeout(() => {
            firstInvalid.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            firstInvalid.focus();
        }, 300);
    }

    showToastNotification() {
        // Verificar si Toastify está disponible
        if (typeof Toastify === 'undefined') {
            console.warn('Toastify no está disponible');
            return;
        }

        Toastify({
            text: "Por favor complete todos los campos obligatorios",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #f46a6a, #c9302c)",
        }).showToast();
    }
}
