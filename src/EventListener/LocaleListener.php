<?php

namespace App\EventListener;

use App\Service\LocalizationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Listener para establecer automáticamente el idioma en cada request
 */
class LocaleListener implements EventSubscriberInterface
{
    private LocalizationService $localizationService;

    public function __construct(LocalizationService $localizationService)
    {
        $this->localizationService = $localizationService;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Solo procesar el request principal
        if (!$event->isMainRequest()) {
            return;
        }

        // Para rutas API, no usar sesiones (stateless)
        $pathInfo = $request->getPathInfo();
        if (str_starts_with($pathInfo, '/api/')) {
            // Para API, usar locale por defecto o desde header
            $locale = $request->headers->get('Accept-Language', 'es');
            $request->setLocale($locale);
            return;
        }

        // Para rutas web normales, usar el servicio de localización
        $locale = $this->localizationService->getCurrentLocale();
        $request->setLocale($locale);

        // Establecer locale en la sesión para persistencia (solo rutas web)
        if ($request->hasSession()) {
            $session = $request->getSession();
            
            // Si no hay locale en sesión, establecerlo
            if (!$session->has('_locale')) {
                $session->set('_locale', $locale);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Ejecutar temprano para que esté disponible en toda la aplicación
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}