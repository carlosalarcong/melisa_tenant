<?php

namespace App\Controller\Admission;

use App\Controller\AbstractTenantAwareController;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admission/emergency', name: 'app_admission_emergency_')]
class EmergencyAdmissionController extends AbstractTenantAwareController
{
    public function __construct(
        private TenantEntityManager $entityManager
    ) {}

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('admission/emergency/index.html.twig', [
            'page_title' => 'Admisión Urgencia',
        ]);
    }
}

