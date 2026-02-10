<?php

namespace App\Controller\Admission;

use App\Controller\AbstractTenantAwareController;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admission/wizard', name: 'app_admission_wizard_')]
class AdmissionWizardController extends AbstractTenantAwareController
{
    public function __construct(
        private TenantEntityManager $entityManager
    ) {}

    #[Route('/step1/{patientId}', name: 'step1', methods: ['GET', 'POST'])]
    public function step1(Request $request, int $patientId): Response
    {
        if ($request->isMethod('POST')) {
            $request->getSession()->set('admission_wizard', [
                'patient_id' => $patientId,
                'step1_confirmed' => true,
            ]);

            return $this->redirectToRoute('app_admission_wizard_step2');
        }

        return $this->render('admission/wizard/step1.html.twig', [
            'patient_id' => $patientId,
        ]);
    }

    #[Route('/step2', name: 'step2', methods: ['GET', 'POST'])]
    public function step2(Request $request): Response
    {
        $wizardData = $request->getSession()->get('admission_wizard', []);
        if (empty($wizardData['patient_id'])) {
            return $this->redirectToRoute('app_admission_hospitalization_index');
        }

        if ($request->isMethod('POST')) {
            $wizardData['payer'] = (string) $request->request->get('payer', '');
            $wizardData['agreement'] = (string) $request->request->get('agreement', '');
            $request->getSession()->set('admission_wizard', $wizardData);

            return $this->redirectToRoute('app_admission_wizard_step3');
        }

        return $this->render('admission/wizard/step2.html.twig', [
            'wizard' => $wizardData,
        ]);
    }

    #[Route('/step3', name: 'step3', methods: ['GET', 'POST'])]
    public function step3(Request $request): Response
    {
        $wizardData = $request->getSession()->get('admission_wizard', []);
        if (empty($wizardData['patient_id'])) {
            return $this->redirectToRoute('app_admission_hospitalization_index');
        }

        if ($request->isMethod('POST')) {
            $wizardData['service'] = (string) $request->request->get('service', '');
            $wizardData['bed'] = (string) $request->request->get('bed', '');
            $request->getSession()->set('admission_wizard', $wizardData);

            $admissionId = time();
            $request->getSession()->remove('admission_wizard');

            return $this->redirectToRoute('app_admission_wizard_complete', [
                'admissionId' => $admissionId,
            ]);
        }

        return $this->render('admission/wizard/step3.html.twig', [
            'wizard' => $wizardData,
        ]);
    }

    #[Route('/complete/{admissionId}', name: 'complete', methods: ['GET'])]
    public function complete(int $admissionId): Response
    {
        return $this->render('admission/wizard/complete.html.twig', [
            'admission_id' => $admissionId,
        ]);
    }
}

