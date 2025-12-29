import { Controller } from '@hotwired/stimulus';

/**
 * Controller para validación de formato de email
 * 
 * Valida el formato del email en tiempo real
 * Muestra mensaje de error/éxito debajo del campo
 */
export default class extends Controller {
    static targets = ['email', 'message'];

    connect() {
        // Agregar listeners para validación
        this.emailTarget.addEventListener('input', this.boundValidate);
        this.emailTarget.addEventListener('blur', this.boundValidate);
        
        // Validar si ya hay valor
        if (this.emailTarget.value) {
            this.validateEmail();
        }
    }

    validateEmail() {
        const email = this.emailTarget.value.trim();
        
        if (!email) {
            this.clearMessage();
            return;
        }

        if (this.isValidEmail(email)) {
            this.showSuccess('Email válido ✓');
            this.emailTarget.classList.remove('is-invalid');
            this.emailTarget.classList.add('is-valid');
        } else {
            this.showError('Formato de email inválido');
            this.emailTarget.classList.remove('is-valid');
            this.emailTarget.classList.add('is-invalid');
        }
    }

    /**
     * Valida el formato de un email
     * @param {string} email - Email a validar
     * @returns {boolean}
     */
    isValidEmail(email) {
        // Expresión regular para validar email según RFC 5322 (simplificada)
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        // Validación básica
        if (!emailRegex.test(email)) {
            return false;
        }

        // Validaciones adicionales
        const parts = email.split('@');
        if (parts.length !== 2) {
            return false;
        }

        const [localPart, domain] = parts;

        // Validar parte local (antes del @)
        if (localPart.length === 0 || localPart.length > 64) {
            return false;
        }

        // No puede empezar o terminar con punto
        if (localPart.startsWith('.') || localPart.endsWith('.')) {
            return false;
        }

        // No puede tener puntos consecutivos
        if (localPart.includes('..')) {
            return false;
        }

        // Validar dominio (después del @)
        if (domain.length === 0 || domain.length > 255) {
            return false;
        }

        // El dominio debe tener al menos un punto
        if (!domain.includes('.')) {
            return false;
        }

        // No puede empezar o terminar con punto o guión
        if (domain.startsWith('.') || domain.endsWith('.') || 
            domain.startsWith('-') || domain.endsWith('-')) {
            return false;
        }

        // Validar que la extensión del dominio tenga al menos 2 caracteres
        const domainParts = domain.split('.');
        const tld = domainParts[domainParts.length - 1];
        if (tld.length < 2) {
            return false;
        }

        return true;
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
        
        this.emailTarget.classList.remove('is-valid', 'is-invalid');
    }

    connect() {
        // Bind del método para poder agregarlo/removerlo como listener
        this.boundValidate = this.validateEmail.bind(this);
        
        // Agregar listeners
        this.emailTarget.addEventListener('input', this.boundValidate);
        this.emailTarget.addEventListener('blur', this.boundValidate);
        
        // Validar si ya hay valor
        if (this.emailTarget.value) {
            this.validateEmail();
        }
    }

    disconnect() {
        // Limpiar listeners
        if (this.boundValidate) {
            this.emailTarget.removeEventListener('input', this.boundValidate);
            this.emailTarget.removeEventListener('blur', this.boundValidate);
        }
    }
}
