import { Controller } from "@hotwired/stimulus";

/**
 * Controlador para el menú colapsable de mantenedores
 * Maneja la navegación, expansión/colapso de secciones y estados activos
 */
export default class extends Controller {
    static targets = ["section", "item"];
    static values = { 
        current: String,
        expanded: { type: Array, default: ["basico"] }
    };

    connect() {
        this.debug('Menu controller conectado');
        this.initializeMenu();
        this.highlightCurrentItem();
        this.expandRelevantSections();
    }

    /**
     * Inicializa el estado del menú
     */
    initializeMenu() {
        // Cargar estado expandido desde localStorage
        const savedExpanded = localStorage.getItem('menu_expanded_sections');
        if (savedExpanded) {
            try {
                this.expandedValue = JSON.parse(savedExpanded);
            } catch (e) {
                this.debug('Error al cargar estado del menú:', e);
            }
        }

        // Aplicar estados guardados
        this.applyExpandedState();
    }

    /**
     * Maneja el click en un item del menú
     */
    selectItem(event) {
        event.preventDefault();
        
        const item = event.currentTarget;
        const url = item.href;
        const itemName = this.extractItemName(url);

        this.debug('Item seleccionado:', itemName);

        // Actualizar estado visual
        this.updateActiveItem(item);
        
        // Guardar selección actual
        this.currentValue = itemName;
        localStorage.setItem('menu_current_item', itemName);

        // Navegar a la URL
        window.location.href = url;
    }

    /**
     * Alterna la expansión de una sección del acordeón
     */
    toggleSection(event) {
        const button = event.currentTarget;
        const sectionName = this.extractSectionName(button);
        const isExpanded = this.isSectionExpanded(sectionName);

        this.debug('Toggle sección:', sectionName, 'expandida:', isExpanded);

        if (isExpanded) {
            this.collapseSection(sectionName);
        } else {
            this.expandSection(sectionName);
        }

        // Guardar estado
        this.saveExpandedState();
    }

    /**
     * Expande una sección específica
     */
    expandSection(sectionName) {
        if (!this.expandedValue.includes(sectionName)) {
            this.expandedValue = [...this.expandedValue, sectionName];
        }
        
        const section = this.findSectionElement(sectionName);
        if (section) {
            const collapse = section.querySelector('.accordion-collapse');
            if (collapse) {
                collapse.classList.add('show');
                const button = section.querySelector('.accordion-button');
                if (button) {
                    button.classList.remove('collapsed');
                    button.setAttribute('aria-expanded', 'true');
                }
            }
        }
    }

    /**
     * Colapsa una sección específica
     */
    collapseSection(sectionName) {
        this.expandedValue = this.expandedValue.filter(name => name !== sectionName);
        
        const section = this.findSectionElement(sectionName);
        if (section) {
            const collapse = section.querySelector('.accordion-collapse');
            if (collapse) {
                collapse.classList.remove('show');
                const button = section.querySelector('.accordion-button');
                if (button) {
                    button.classList.add('collapsed');
                    button.setAttribute('aria-expanded', 'false');
                }
            }
        }
    }

    /**
     * Resalta el item actualmente seleccionado
     */
    highlightCurrentItem() {
        const current = this.currentValue || localStorage.getItem('menu_current_item');
        if (!current) return;

        const items = this.element.querySelectorAll('.menu-item');
        items.forEach(item => {
            const itemName = this.extractItemName(item.href);
            if (itemName === current) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    /**
     * Actualiza el item activo visualmente
     */
    updateActiveItem(selectedItem) {
        // Remover clase active de todos los items
        const items = this.element.querySelectorAll('.menu-item');
        items.forEach(item => item.classList.remove('active'));

        // Agregar clase active al item seleccionado
        selectedItem.classList.add('active');

        // Mostrar efecto de click
        this.showClickEffect(selectedItem);
    }

    /**
     * Efecto visual de click en el item
     */
    showClickEffect(item) {
        item.style.transform = 'scale(0.98)';
        item.style.transition = 'transform 0.1s ease-in-out';
        
        setTimeout(() => {
            item.style.transform = 'scale(1)';
        }, 100);
    }

    /**
     * Expande las secciones relevantes al item actual
     */
    expandRelevantSections() {
        const current = this.currentValue || localStorage.getItem('menu_current_item');
        if (!current) return;

        // Determinar qué sección debe estar expandida
        const sectionMap = {
            'sexo': 'basico',
            'region': 'basico',
            // Agregar más mappings según sea necesario
        };

        const requiredSection = sectionMap[current];
        if (requiredSection && !this.isSectionExpanded(requiredSection)) {
            this.expandSection(requiredSection);
            this.saveExpandedState();
        }
    }

    /**
     * Aplica el estado expandido guardado
     */
    applyExpandedState() {
        this.expandedValue.forEach(sectionName => {
            this.expandSection(sectionName);
        });
    }

    /**
     * Guarda el estado expandido en localStorage
     */
    saveExpandedState() {
        localStorage.setItem('menu_expanded_sections', JSON.stringify(this.expandedValue));
    }

    /**
     * Verifica si una sección está expandida
     */
    isSectionExpanded(sectionName) {
        return this.expandedValue.includes(sectionName);
    }

    /**
     * Encuentra el elemento DOM de una sección
     */
    findSectionElement(sectionName) {
        return this.element.querySelector(`[data-section="${sectionName}"]`) ||
               this.element.querySelector(`#${sectionName}Collapse`)?.closest('.accordion-item');
    }

    /**
     * Extrae el nombre del item desde una URL
     */
    extractItemName(url) {
        if (!url) return '';
        
        // Extraer desde URLs como '/mantenedores/basico/sexo'
        const matches = url.match(/\/mantenedores\/[^\/]+\/([^\/\?]+)/);
        return matches ? matches[1] : '';
    }

    /**
     * Extrae el nombre de la sección desde un botón
     */
    extractSectionName(button) {
        const target = button.getAttribute('data-bs-target');
        if (target) {
            // Extraer desde '#basicoCollapse' -> 'basico'
            return target.replace('#', '').replace('Collapse', '').toLowerCase();
        }
        
        return button.closest('.accordion-item')?.id || '';
    }

    /**
     * Busca items del menú por texto
     */
    searchItems(query) {
        const items = this.element.querySelectorAll('.menu-item');
        const results = [];

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(query.toLowerCase())) {
                results.push({
                    element: item,
                    text: item.textContent.trim(),
                    url: item.href
                });
            }
        });

        return results;
    }

    /**
     * Resalta items que coinciden con una búsqueda
     */
    highlightSearchResults(query) {
        const items = this.element.querySelectorAll('.menu-item');
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            const matches = text.includes(query.toLowerCase());
            
            item.classList.toggle('search-match', matches && query.length > 0);
            item.classList.toggle('search-no-match', !matches && query.length > 0);
        });
    }

    /**
     * Limpia el resaltado de búsqueda
     */
    clearSearchHighlight() {
        const items = this.element.querySelectorAll('.menu-item');
        items.forEach(item => {
            item.classList.remove('search-match', 'search-no-match');
        });
    }

    /**
     * Colapsa todas las secciones
     */
    collapseAll() {
        this.expandedValue.forEach(sectionName => {
            this.collapseSection(sectionName);
        });
        this.expandedValue = [];
        this.saveExpandedState();
    }

    /**
     * Expande todas las secciones
     */
    expandAll() {
        const sections = this.element.querySelectorAll('.accordion-item');
        const allSections = [];
        
        sections.forEach(section => {
            const button = section.querySelector('.accordion-button');
            if (button) {
                const sectionName = this.extractSectionName(button);
                if (sectionName) {
                    allSections.push(sectionName);
                    this.expandSection(sectionName);
                }
            }
        });
        
        this.expandedValue = allSections;
        this.saveExpandedState();
    }

    /**
     * Refresca el estado del menú
     */
    refresh() {
        this.highlightCurrentItem();
        this.expandRelevantSections();
    }

    /**
     * Logging para debugging
     */
    debug(...args) {
        if (window.MantenedorConfig?.debug) {
            console.log('[Menu Controller]', ...args);
        }
    }

    /**
     * Cleanup al desconectar
     */
    disconnect() {
        this.debug('Menu controller desconectado');
    }
}