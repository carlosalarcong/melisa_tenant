<?php

namespace App\EventListener;

use Doctrine\DBAL\Connection;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener(event: KernelEvents::REQUEST, method: 'onKernelRequest' , priority: 25)]
class InitialRequestListener
{

    public function __construct(private Connection $connection

    ){
        $this->connection = $connection;
    }

    public function onKernelRequest(RequestEvent $event): void
    {

        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->attributes->get('_route') !== 'app_login') {
            return;
        }




    }
}