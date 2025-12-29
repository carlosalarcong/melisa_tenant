import { Controller } from '@hotwired/stimulus';

/**
 * Controller para generación automática de contraseñas seguras
 * 
 * Genera contraseñas aleatorias y permite copiarlas al portapapeles
 */
export default class extends Controller {
    static targets = ['password'];
    
    generatedPassword = null;
    isGenerated = false;

    /**
     * Genera una contraseña aleatoria segura
     */
    generatePassword(event) {
        // Si ya está generada, copiar
        if (this.isGenerated) {
            this.copyPasswordFromButton(event);
            return;
        }

        const length = 12;
        const charset = {
            lowercase: 'abcdefghijklmnopqrstuvwxyz',
            uppercase: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            numbers: '0123456789',
            special: '!@#$%&*-_=+',
        };

        // Garantizar al menos un carácter de cada tipo
        let password = '';
        password += this.getRandomChar(charset.lowercase);
        password += this.getRandomChar(charset.uppercase);
        password += this.getRandomChar(charset.numbers);
        password += this.getRandomChar(charset.special);

        // Completar con caracteres aleatorios
        const allChars = charset.lowercase + charset.uppercase + charset.numbers + charset.special;
        for (let i = password.length; i < length; i++) {
            password += this.getRandomChar(allChars);
        }

        // Mezclar los caracteres
        password = this.shuffleString(password);

        // Guardar la contraseña generada
        this.generatedPassword = password;
        this.isGenerated = true;

        // Establecer en el campo
        this.passwordTarget.value = password;

        // Cambiar el botón a "Copiar"
        this.changeButtonToCopy(event.currentTarget);

        // Trigger input event para validaciones
        this.passwordTarget.dispatchEvent(new Event('input', { bubbles: true }));
    }

    /**
     * Cambia el botón de "Generar" a "Copiar"
     */
    changeButtonToCopy(button) {
        button.innerHTML = '<i class="ri-file-copy-line"></i> Copiar';
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-success');
        button.title = 'Copiar contraseña al portapapeles';
    }

    /**
     * Copia la contraseña desde el botón
     */
    copyPasswordFromButton(event) {
        if (!this.generatedPassword) {
            return;
        }

        // Verificar si el navegador soporta clipboard API
        if (!navigator.clipboard || !navigator.clipboard.writeText) {
            // Fallback: seleccionar el texto del campo
            this.passwordTarget.select();
            this.passwordTarget.setSelectionRange(0, 99999); // Para móviles
            
            try {
                document.execCommand('copy');
                const button = event.currentTarget;
                const originalHTML = button.innerHTML;
                
                button.innerHTML = '<i class="ri-check-line"></i> Copiado!';
                button.classList.remove('btn-success');
                button.classList.add('btn-info');

                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('btn-info');
                    button.classList.add('btn-success');
                }, 2000);
            } catch (err) {
                console.error('Error al copiar:', err);
                alert('No se pudo copiar. Por favor, selecciona y copia manualmente: Ctrl+C');
            }
            return;
        }

        navigator.clipboard.writeText(this.generatedPassword).then(() => {
            const button = event.currentTarget;
            const originalHTML = button.innerHTML;
            
            // Cambiar temporalmente a "Copiado"
            button.innerHTML = '<i class="ri-check-line"></i> Copiado!';
            button.classList.remove('btn-success');
            button.classList.add('btn-info');

            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('btn-info');
                button.classList.add('btn-success');
            }, 2000);
        }).catch(err => {
            console.error('Error al copiar:', err);
            alert('No se pudo copiar la contraseña al portapapeles');
        });
    }

    /**
     * Obtiene un carácter aleatorio de un string
     */
    getRandomChar(str) {
        return str.charAt(Math.floor(Math.random() * str.length));
    }

    /**
     * Mezcla los caracteres de un string
     */
    shuffleString(str) {
        const arr = str.split('');
        for (let i = arr.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [arr[i], arr[j]] = [arr[j], arr[i]];
        }
        return arr.join('');
    }

    /**
     * Muestra la contraseña generada con opción de copiar
     */
    showGeneratedPassword(password) {
        if (this.hasGeneratedTarget) {
            this.generatedTarget.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ri-key-2-line me-2"></i>
                    <strong>Contraseña generada:</strong>
                    <code class="mx-2 user-select-all">${password}</code>
                    <button type="button" class="btn btn-sm btn-outline-success ms-2" data-action="click->admin-user--password-generator#copyPassword" data-password="${password}">
                        <i class="ri-file-copy-line"></i> Copiar
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
    }

    /**
     * Copia la contraseña al portapapeles
     */
    copyPassword(event) {
        const password = event.currentTarget.dataset.password;
        
        navigator.clipboard.writeText(password).then(() => {
            // Cambiar el botón temporalmente
            const button = event.currentTarget;
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="ri-check-line"></i> Copiado!';
            button.classList.remove('btn-outline-success');
            button.classList.add('btn-success');

            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-success');
            }, 2000);
        }).catch(err => {
            console.error('Error al copiar:', err);
            alert('No se pudo copiar la contraseña al portapapeles');
        });
    }

    /**
     * Limpia el campo de contraseña
     */
    clearPassword() {
        this.passwordTarget.value = '';
        this.passwordConfirmTarget.value = '';
        
        if (this.hasGeneratedTarget) {
            this.generatedTarget.innerHTML = '';
        }

        this.passwordTarget.dispatchEvent(new Event('input', { bubbles: true }));
    }
}
