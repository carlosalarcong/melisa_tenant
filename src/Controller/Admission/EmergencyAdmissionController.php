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

    #[Route('/create/{patientId}', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, int $patientId): Response
    {
        if ($request->isMethod('POST')) {
            $urgencyId = time();

            return $this->redirectToRoute('app_admission_emergency_view', [
                'id' => $urgencyId,
            ]);
        }

        return $this->render('admission/emergency/_form.html.twig', [
            'patient_id' => $patientId,
        ]);
    }

    #[Route('/{id}/view', name: 'view', methods: ['GET'])]
    public function view(int $id): Response
    {
        return $this->render('admission/view.html.twig', [
            'admission_id' => $id,
            'admission_type' => 'urgencia',
        ]);
    }
}
