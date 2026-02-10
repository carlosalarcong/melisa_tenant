import { Controller } from '@hotwired/stimulus';

/**
 * Stimulus controller para manejar confirmaciones de eliminación con SweetAlert2
 * 
 * Uso en template:
 * <form data-controller="confirm-delete" 
 *       data-confirm-delete-title-value="¿Estás seguro?" 
 *       data-confirm-delete-text-value="Esta acción no se puede deshacer"
 *       data-action="submit->confirm-delete#confirm">
 *   <button type="submit">Eliminar</button>
 * </form>
 */
export default class extends Controller {
    static values = {
        title: { type: String, default: '¿Estás seguro?' },
        text: { type: String, default: 'Esta acción no se puede deshacer' },
        confirmButtonText: { type: String, default: 'Sí, eliminar' },
        cancelButtonText: { type: String, default: 'Cancelar' },
        icon: { type: String, default: 'warning' }
    }

    async confirm(event) {
        // Prevenir el submit por defecto
        event.preventDefault();

        try {
            const result = await Swal.fire({
                title: this.titleValue,
                text: this.textValue,
                icon: this.iconValue,
                showCancelButton: true,
                confirmButtonColor: '#dc3545', // danger color
                cancelButtonColor: '#6c757d',   // secondary color
                confirmButtonText: this.confirmButtonTextValue,
                cancelButtonText: this.cancelButtonTextValue,
                reverseButtons: true, // Cancelar a la izquierda, Confirmar a la derecha
                focusCancel: true     // Focus en cancelar por seguridad
            });

            // Si el usuario confirma, enviar el formulario
            if (result.isConfirmed) {
                // Deshabilitar el controller temporalmente para permitir el submit real
                this.element.removeAttribute('data-action');
                this.element.submit();
            }
        } catch (error) {
            console.error('Error en confirmación de eliminación:', error);
        }
    }
}
