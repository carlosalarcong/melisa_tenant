import { startStimulusApp } from '@symfony/stimulus-bundle';

const app = startStimulusApp();
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

// Exponer la aplicación Stimulus globalmente para que pueda ser accedida desde otros scripts
window.Stimulus = app;

// También exportar la aplicación para uso en módulos ES6
export { app };
