import { Controller } from '@hotwired/stimulus';

/**
 * Controller para validación de RUT chileno
 * 
 * Valida el RUT en tiempo real cuando el tipo de documento seleccionado es "RUT"
 * Muestra mensaje de error/éxito debajo del campo
 */
export default class extends Controller {
    static targets = ['documentType', 'identification', 'message'];
    static values = {
        rutTypeId: { type: Number, default: 2 } // ID de "RUT" en identification_type
    };

    connect() {
        // Verificar estado inicial
        this.checkDocumentType();
    }

    checkDocumentType() {
        const selectedType = this.documentTypeTarget.value;
        
        if (selectedType == this.rutTypeIdValue) {
            this.enableRutValidation();
        } else {
            this.disableRutValidation();
        }
    }

    enableRutValidation() {
        // Agregar listener para validación en tiempo real
        this.identificationTarget.addEventListener('input', this.boundValidateRut);
        this.identificationTarget.addEventListener('blur', this.boundValidateRut);
        
        // Cambiar placeholder
        this.identificationTarget.placeholder = 'Ej: 12345678-9';
        
        // Validar si ya hay valor
        if (this.identificationTarget.value) {
            this.validateRut();
        }
    }

    disableRutValidation() {
        // Remover listeners
        if (this.boundValidateRut) {
            this.identificationTarget.removeEventListener('input', this.boundValidateRut);
            this.identificationTarget.removeEventListener('blur', this.boundValidateRut);
        }
        
        // Limpiar mensaje
        this.clearMessage();
        
        // Cambiar placeholder
        this.identificationTarget.placeholder = 'Ingrese identificación';
    }

    validateRut() {
        const rut = this.identificationTarget.value.trim();
        
        if (!rut) {
            this.clearMessage();
            return;
        }

        if (this.isValidRut(rut)) {
            this.showSuccess('RUT válido ✓');
            this.identificationTarget.classList.remove('is-invalid');
            this.identificationTarget.classList.add('is-valid');
        } else {
            this.showError('RUT inválido. Formato: 12345678-9');
            this.identificationTarget.classList.remove('is-valid');
            this.identificationTarget.classList.add('is-invalid');
        }
    }

    /**
     * Valida un RUT chileno
     * @param {string} rut - RUT en formato 12345678-9
     * @returns {boolean}
     */
    isValidRut(rut) {
        // Limpiar formato
        rut = rut.replace(/\./g, '').replace(/-/g, '').toUpperCase();
        
        // Verificar longitud mínima
        if (rut.length < 2) {
            return false;
        }

        // Separar número y dígito verificador
        const rutNumber = rut.slice(0, -1);
        const dvInput = rut.slice(-1);

        // Verificar que el número sea numérico
        if (!/^\d+$/.test(rutNumber)) {
            return false;
        }

        // Calcular dígito verificador
        const dvCalculated = this.calculateDV(rutNumber);

        // Comparar
        return dvInput === dvCalculated;
    }

    /**
     * Calcula el dígito verificador de un RUT
     * @param {string} rutNumber - Número del RUT sin dígito verificador
     * @returns {string} - Dígito verificador ('0'-'9' o 'K')
     */
    calculateDV(rutNumber) {
        let sum = 0;
        let multiplier = 2;

        // Recorrer de derecha a izquierda
        for (let i = rutNumber.length - 1; i >= 0; i--) {
            sum += parseInt(rutNumber[i]) * multiplier;
            multiplier = multiplier === 7 ? 2 : multiplier + 1;
        }

        const remainder = sum % 11;
        const dv = 11 - remainder;

        if (dv === 11) return '0';
        if (dv === 10) return 'K';
        return dv.toString();
    }

    /**
     * Formatea un RUT con puntos y guión
     * @param {string} rut - RUT sin formato
     * @returns {string} - RUT formateado
     */
    formatRut(rut) {
        // Limpiar
        rut = rut.replace(/\./g, '').replace(/-/g, '');
        
        if (rut.length < 2) {
            return rut;
        }

        // Separar número y dígito verificador
        const rutNumber = rut.slice(0, -1);
        const dv = rut.slice(-1);

        // Agregar puntos al número
        const formattedNumber = rutNumber.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        return `${formattedNumber}-${dv}`;
    }

    showError(message) {
        if (this.hasMessageTarget) {
            this.messageTarget.textContent = message;
            this.messageTarget.className = 'text-danger small mt-1';
            this.messageTarget.style.display = 'block';
        }
    }

    showSuccess(message) {
        if (this.hasMessageTarget) {
            this.messageTarget.textContent = message;
            this.messageTarget.className = 'text-success small mt-1';
            this.messageTarget.style.display = 'block';
        }
    }

    clearMessage() {
        if (this.hasMessageTarget) {
            this.messageTarget.textContent = '';
            this.messageTarget.style.display = 'none';
        }
        
        this.identificationTarget.classList.remove('is-valid', 'is-invalid');
    }

    // Método que se llama cuando cambia el tipo de documento
    documentTypeChanged() {
        this.checkDocumentType();
        
        // Limpiar el campo de identificación si se cambia el tipo
        if (this.identificationTarget.value) {
            this.identificationTarget.value = '';
            this.clearMessage();
        }
    }

    connect() {
        // Bind del método validateRut para poder agregarlo/removerlo como listener
        this.boundValidateRut = this.validateRut.bind(this);
        
        // Verificar estado inicial
        this.checkDocumentType();
    }

    disconnect() {
        this.disableRutValidation();
    }
}
