import { Application } from '@hotwired/stimulus';

// Iniciar Stimulus
const app = Application.start();

// Auto-registrar todos los controllers automáticamente
// Busca recursivamente en ./controllers/ todos los archivos *_controller.js
const controllers = require.context('./controllers', true, /_controller\.js$/);

controllers.keys().forEach((key) => {
    // Extraer el nombre del controller desde la ruta
    // Ejemplo: ./admin_user/username_generator_controller.js → admin_user--username-generator
    const controllerName = key
        .replace('./', '')                           // Eliminar ./
        .replace(/_controller\.js$/, '')             // Eliminar _controller.js
        .replace(/\//g, '--')                        // Reemplazar / por --
        .replace(/_/g, '-');                         // Reemplazar _ por -
    
    const module = controllers(key);

    app.register(controllerName, module.default);
});

// Exponer globalmente
window.Stimulus = app;
export { app };
