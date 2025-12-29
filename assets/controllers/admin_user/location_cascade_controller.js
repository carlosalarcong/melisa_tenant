import { Controller } from '@hotwired/stimulus';

/**
 * Controller para manejar la cascada Sucursal → Unidad → Servicio
 * 
 * Targets:
 * - branch: Select de sucursal
 * - department: Select de unidad (se habilita al seleccionar sucursal)
 * - service: Select de servicio (se habilita al seleccionar unidad)
 */
export default class extends Controller {
    static targets = ['branch', 'department', 'service'];

    connect() {
        console.log('Location cascade controller connected');
        
        // Deshabilitar unidad y servicio al inicio
        if (this.hasDepartmentTarget) {
            this.departmentTarget.disabled = true;
        }
        if (this.hasServiceTarget) {
            this.serviceTarget.disabled = true;
        }
    }

    /**
     * Cargar unidades cuando se selecciona una sucursal
     */
    async loadDepartments(event) {
        const branchId = event.target.value;
        
        if (!branchId) {
            this.resetDepartments();
            this.resetServices();
            return;
        }

        try {
            // Mostrar loading en el select
            this.departmentTarget.disabled = true;
            this.departmentTarget.innerHTML = '<option value="">Cargando...</option>';

            const response = await fetch(`/admin/users/ajax/branch/${branchId}/departments`);
            const data = await response.json();

            if (data.success && data.departments) {
                this.populateDepartments(data.departments);
            } else {
                this.showError('No se pudieron cargar las unidades');
            }
        } catch (error) {
            console.error('Error loading departments:', error);
            this.showError('Error al cargar las unidades');
        }

        // Resetear servicios
        this.resetServices();
    }

    /**
     * Cargar servicios cuando se selecciona una unidad
     */
    async loadServices(event) {
        const departmentId = event.target.value;
        
        if (!departmentId) {
            this.resetServices();
            return;
        }

        try {
            // Mostrar loading en el select
            this.serviceTarget.disabled = true;
            this.serviceTarget.innerHTML = '<option value="">Cargando...</option>';

            const response = await fetch(`/admin/users/ajax/department/${departmentId}/services`);
            const data = await response.json();

            if (data.success && data.services) {
                this.populateServices(data.services);
            } else {
                this.showError('No se pudieron cargar los servicios');
            }
        } catch (error) {
            console.error('Error loading services:', error);
            this.showError('Error al cargar los servicios');
        }
    }

    /**
     * Poblar el select de unidades
     */
    populateDepartments(departments) {
        this.departmentTarget.innerHTML = '<option value="">Seleccionar Unidad</option>';
        
        departments.forEach(dept => {
            const option = document.createElement('option');
            option.value = dept.id;
            option.textContent = dept.name;
            this.departmentTarget.appendChild(option);
        });

        this.departmentTarget.disabled = false;
    }

    /**
     * Poblar el select de servicios
     */
    populateServices(services) {
        this.serviceTarget.innerHTML = '<option value="">Seleccionar Servicio</option>';
        
        services.forEach(service => {
            const option = document.createElement('option');
            option.value = service.id;
            option.textContent = service.name;
            this.serviceTarget.appendChild(option);
        });

        this.serviceTarget.disabled = false;
    }

    /**
     * Resetear el select de unidades
     */
    resetDepartments() {
        if (this.hasDepartmentTarget) {
            this.departmentTarget.innerHTML = '<option value="">Seleccionar Unidad</option>';
            this.departmentTarget.disabled = true;
        }
    }

    /**
     * Resetear el select de servicios
     */
    resetServices() {
        if (this.hasServiceTarget) {
            this.serviceTarget.innerHTML = '<option value="">Seleccionar Servicio</option>';
            this.serviceTarget.disabled = true;
        }
    }

    /**
     * Mostrar mensaje de error
     */
    showError(message) {
        console.error(message);
        // TODO: Implementar notificación visual al usuario
    }
}
