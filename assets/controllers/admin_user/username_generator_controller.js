import { Controller } from '@hotwired/stimulus';

/**
 * Stimulus controller para generación automática de username
 * 
 * Genera username basado en nombre y apellidos:
 * - Formato: primera letra del nombre + apellido paterno
 * - Todo en minúsculas
 * - Sin tildes ni caracteres especiales
 * - Solo letras y números
 * 
 * Ejemplo:
 * - Juan Pérez → jperez
 * - María González Silva → mgonzalez
 */
export default class extends Controller {
    static targets = ['name', 'lastName', 'username'];

    connect() {
        // Auto-generar cuando cambian los campos
        if (this.hasNameTarget) {
            this.nameTarget.addEventListener('blur', () => this.autoGenerateUsername());
        }
        
        if (this.hasLastNameTarget) {
            this.lastNameTarget.addEventListener('blur', () => this.autoGenerateUsername());
        }
    }

    /**
     * Genera username automáticamente basado en nombre y apellido
     */
    autoGenerateUsername() {
        if (!this.hasNameTarget || !this.hasLastNameTarget || !this.hasUsernameTarget) {
            return;
        }

        const nombre = this.nameTarget.value.trim();
        const apellido = this.lastNameTarget.value.trim();

        // Si no hay nombre o apellido, no generar
        if (!nombre || !apellido) {
            return;
        }

        // Solo generar si el campo está vacío (no sobrescribir)
        const currentUsername = this.usernameTarget.value.trim();
        if (currentUsername !== '') {
            return;
        }

        // Generar username: primera letra del nombre + apellido completo
        const primeraLetra = nombre.charAt(0);
        const username = (primeraLetra + apellido)
            .toLowerCase()
            .normalize('NFD') // Normalizar caracteres Unicode
            .replace(/[\u0300-\u036f]/g, '') // Quitar tildes
            .replace(/[^a-z0-9]/g, ''); // Solo letras y números

        // Remover readonly temporalmente para permitir escritura desde JS
        const wasReadonly = this.usernameTarget.hasAttribute('readonly');
        if (wasReadonly) {
            this.usernameTarget.removeAttribute('readonly');
        }

        this.usernameTarget.value = username;
        
        // Restaurar readonly
        if (wasReadonly) {
            this.usernameTarget.setAttribute('readonly', 'readonly');
        }
        
        // Disparar eventos de cambio para validación AJAX
        this.usernameTarget.dispatchEvent(new Event('change', { bubbles: true }));
        this.usernameTarget.dispatchEvent(new Event('input', { bubbles: true }));
    }

    /**
     * Acción manual para regenerar username (botón)
     */
    generate() {
        // Limpiar el campo para permitir regeneración
        if (this.hasUsernameTarget) {
            const wasReadonly = this.usernameTarget.hasAttribute('readonly');
            if (wasReadonly) {
                this.usernameTarget.removeAttribute('readonly');
            }
            
            this.usernameTarget.value = '';
            
            if (wasReadonly) {
                this.usernameTarget.setAttribute('readonly', 'readonly');
            }
        }
        
        this.autoGenerateUsername();
    }
}
