/**
 * CONTROLADOR ESPECÍFICO PARA MANTENEDOR DE PAÍSES - STIMULUS
 * =========================================================
 * 
 * Controlador simplificado para el mantenedor de países.
 * Version temporal sin herencia para resolver problema de carga.
 * 
 * @author Equipo Melisa - Frontend
 * @version 1.1
 * @since 2025-10-27
 */

import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    // ==========================================
    // CONFIGURACIÓN STIMULUS
    // ==========================================
    static targets = [
        "modal",
        "form", 
        "title",
        "idField",
        "nombrePais",
        "nombreGentilicio", 
        "activo",
        "submitButton"
    ]
    
    static values = {
        entityName: String,
        entityNamePlural: String,
        apiBase: String,
        modalId: String
    }
    
    // ==========================================
    // INICIALIZACIÓN
    // ==========================================
    
    connect() {
        console.log('🏳️ Controlador de países conectado');
    }
    
    disconnect() {
        console.log('🏳️ Controlador de países desconectado');
    }
    
    // ==========================================
    // MÉTODOS PARA EVENTOS STIMULUS
    // ==========================================
    
    /**
     * Maneja la acción de crear nuevo país
     */
    handleCreate(event) {
        console.log('Stimulus: Crear país');
        event.preventDefault();
        
        // Limpiar formulario
        if (this.hasFormTarget) {
            this.formTarget.reset();
        }
        
        // Configurar modal para crear
        if (this.hasTitleTarget) {
            this.titleTarget.textContent = 'Crear País';
        }
        
        if (this.hasIdFieldTarget) {
            this.idFieldTarget.value = '';
        }
        
        if (this.hasActivoTarget) {
            this.activoTarget.checked = true;
        }
    }
    
    /**
     * Maneja la acción de editar país
     */
    handleEdit(event) {
        console.log('Stimulus: Editar país');
        event.preventDefault();
        
        const id = event.target.getAttribute('data-id');
        console.log('ID a editar:', id);
        
        // Configurar modal para editar
        if (this.hasTitleTarget) {
            this.titleTarget.textContent = 'Editar País';
        }
        
        if (this.hasIdFieldTarget) {
            this.idFieldTarget.value = id;
        }
        
        // Aquí cargarías los datos del país desde la API
        // Por ahora dejamos placeholder
    }
    
    /**
     * Maneja la acción de eliminar país
     */
    handleDelete(event) {
        console.log('Stimulus: Eliminar país');
        event.preventDefault();
        
        const id = event.target.getAttribute('data-id');
        const name = event.target.getAttribute('data-name');
        
        console.log('ID a eliminar:', id, 'Nombre:', name);
        
        // Aquí mostrarías confirmación con SweetAlert
        if (confirm(`¿Estás seguro de eliminar el país "${name}"?`)) {
            console.log('Confirmado eliminar país');
            // Aquí iría la llamada AJAX para eliminar
        }
    }
    
    /**
     * Acción para generar gentilicio automáticamente
     */
    autoGenerateGentilicio() {
        if (!this.hasNombrePaisTarget || !this.hasNombreGentilicioTarget) return;
        
        const paisNombre = this.nombrePaisTarget.value.trim();
        if (!paisNombre) return;
        
        // Reglas básicas para generar gentilicios
        let gentilicio = paisNombre;
        
        // Casos específicos conocidos
        const gentilicios = {
            'Chile': 'Chileno',
            'Argentina': 'Argentino', 
            'Brasil': 'Brasileño',
            'Perú': 'Peruano',
            'Colombia': 'Colombiano',
            'Venezuela': 'Venezolano',
            'Ecuador': 'Ecuatoriano',
            'Uruguay': 'Uruguayo',
            'Paraguay': 'Paraguayo',
            'Bolivia': 'Boliviano'
        };
        
        if (gentilicios[paisNombre]) {
            gentilicio = gentilicios[paisNombre];
        } else {
            // Regla general: agregar terminación
            if (paisNombre.endsWith('a')) {
                gentilicio = paisNombre.slice(0, -1) + 'ano';
            } else {
                gentilicio = paisNombre + 'ano';
            }
        }
        
        this.nombreGentilicioTarget.value = gentilicio;
    }
    
    /**
     * Acción para limpiar formulario
     */
    clearForm() {
        if (this.hasFormTarget) {
            this.formTarget.reset();
        }
        if (this.hasActivoTarget) {
            this.activoTarget.checked = true;
        }
    }
    
    /**
     * Acción para convertir texto a formato título
     */
    formatToTitle(event) {
        const field = event.target;
        const words = field.value.toLowerCase().split(' ');
        const titleCase = words.map(word => 
            word.charAt(0).toUpperCase() + word.slice(1)
        ).join(' ');
        field.value = titleCase;
    }
}