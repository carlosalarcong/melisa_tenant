import { Controller } from '@hotwired/stimulus';

/**
 * Controller para manejar la fecha de expiración del usuario
 * 
 * Muestra/oculta el campo de fecha según se seleccione "Indefinido" o "Definir fecha"
 */
export default class extends Controller {
    static targets = ['dateContainer'];

    connect() {
        // Verificar el estado inicial
        this.checkInitialState();
    }

    checkInitialState() {
        const definiteRadio = document.querySelector('input[name*="expirationDateType"][value="definite"]');
        if (definiteRadio && definiteRadio.checked) {
            this.showDateField();
        } else {
            this.hideDateField();
        }
    }

    toggleDate(event) {
        const value = event.target.value;
        
        if (value === 'definite') {
            this.showDateField();
        } else {
            this.hideDateField();
        }
    }

    showDateField() {
        if (this.hasDateContainerTarget) {
            this.dateContainerTarget.style.display = 'block';
            const input = this.dateContainerTarget.querySelector('input');
            if (input) {
                input.required = true;
            }
        }
    }

    hideDateField() {
        if (this.hasDateContainerTarget) {
            this.dateContainerTarget.style.display = 'none';
            const input = this.dateContainerTarget.querySelector('input');
            if (input) {
                input.required = false;
                input.value = ''; // Limpiar el valor
            }
        }
    }
}
