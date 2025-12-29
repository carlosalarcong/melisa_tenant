import { Controller } from '@hotwired/stimulus';

/**
 * Controlador que muestra/oculta la sección completa de Datos Profesionales según el rol
 * 
 * Al inicio: Los 5 combos institucionales están habilitados, Datos Profesionales oculto
 * 
 * Si el rol es "Profesional Clínico" (isClinicalProfessional = true):
 *   - Muestra: Toda la sección Datos Profesionales
 * 
 * Si el rol es "Administrativo" (isClinicalProfessional = false):
 *   - Oculta: Toda la sección Datos Profesionales
 */
export default class extends Controller {
    static targets = [
        'role',
        'professionalSection',
        'professionalType',
        'specialties',
        'healthInsurances',
        'rcm',
        'superintendentRegistry',
        'observation',
        'webObservation',
        'overbookingQuantity',
        'isEmergencyProfessional',
        'isIntegrationProfessional'
    ];

    connect() {
        console.log('Role toggle controller connected');
        // Al inicio, ocultar la sección de Datos Profesionales
        this.hideProfessionalSection();
        
        // Si hay un rol pre-seleccionado (en edición), verificar su estado
        if (this.hasRoleTarget && this.roleTarget.value) {
            this.checkRole(this.roleTarget.value);
        }
    }

    /**
     * Se ejecuta cuando cambia el rol seleccionado
     */
    async onRoleChange(event) {
        const roleId = event.target.value;

        if (!roleId) {
            this.hideProfessionalSection();
            return;
        }

        await this.checkRole(roleId);
    }

    /**
     * Verifica el tipo de rol y muestra/oculta la sección profesional
     */
    async checkRole(roleId) {
        try {
            const response = await fetch(`/admin/users/ajax/identify-role/${roleId}`);
            const data = await response.json();

            if (data.success && data.isClinicalProfessional) {
                console.log(`Rol ${data.roleName} es profesional clínico - mostrando sección profesional`);
                this.showProfessionalSection();
            } else {
                console.log(`Rol ${data.roleName} NO es profesional clínico - ocultando sección profesional`);
                this.hideProfessionalSection();
            }

        } catch (error) {
            console.error('Error identifying role:', error);
            this.hideProfessionalSection();
        }
    }

    /**
     * Muestra la sección completa de Datos Profesionales
     */
    showProfessionalSection() {
        if (this.hasProfessionalSectionTarget) {
            this.professionalSectionTarget.style.display = 'contents';
            console.log('Sección Datos Profesionales mostrada');
        }
    }

    /**
     * Oculta la sección completa de Datos Profesionales
     */
    hideProfessionalSection() {
        if (this.hasProfessionalSectionTarget) {
            this.professionalSectionTarget.style.display = 'none';
            console.log('Sección Datos Profesionales oculta');
        }
    }
}
