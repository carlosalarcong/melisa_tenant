<?php

namespace App\Controller\Admission;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Tenant\AdmissionRecord;
use App\Entity\Tenant\Person;
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
        $patient = $this->entityManager->find(Person::class, $patientId);
        if (!$patient instanceof Person) {
            $this->addFlash('danger', 'Paciente no encontrado.');
            return $this->redirectToRoute('app_admission_hospitalization_index');
        }

        $admissionType = (string) $request->query->get('type', 'hospitalaria');
        if (!in_array($admissionType, ['hospitalaria', 'pre'], true)) {
            $admissionType = 'hospitalaria';
        }

        if ($request->isMethod('POST')) {
            $record = new AdmissionRecord();
            $record->setPerson($patient);
            $record->setAdmissionType($admissionType);
            $record->setStatus('draft');

            $this->entityManager->persist($record);
            $this->entityManager->flush();

            $request->getSession()->set('admission_wizard', [
                'patient_id' => $patientId,
                'admission_record_id' => $record->getId(),
                'admission_type' => $admissionType,
                'step1_confirmed' => true,
            ]);

            return $this->redirectToRoute('app_admission_wizard_step2');
        }

        return $this->render('admission/wizard/step1.html.twig', [
            'patient_id' => $patientId,
            'patient' => $patient,
            'admission_type' => $admissionType,
        ]);
    }

    #[Route('/step2', name: 'step2', methods: ['GET', 'POST'])]
    public function step2(Request $request): Response
    {
        $wizardData = $request->getSession()->get('admission_wizard', []);
        if (empty($wizardData['patient_id']) || empty($wizardData['admission_record_id'])) {
            return $this->redirectToRoute('app_admission_hospitalization_index');
        }

        /** @var AdmissionRecord|null $record */
        $record = $this->entityManager->find(AdmissionRecord::class, (int) $wizardData['admission_record_id']);
        if (!$record instanceof AdmissionRecord) {
            $this->addFlash('danger', 'No se encontró la admisión en curso.');
            return $this->redirectToRoute('app_admission_hospitalization_index');
        }

        if ($request->isMethod('POST')) {
            $payerId = $request->request->getInt('payer', 0);
            $agreementId = $request->request->getInt('agreement', 0);
            if ($payerId <= 0 || $agreementId <= 0) {
                $this->addFlash('danger', 'Debes seleccionar financiador y convenio.');
                return $this->render('admission/wizard/step2.html.twig', [
                    'wizard' => $wizardData,
                ]);
            }

            $connection = $this->entityManager->getConnection();
            $payerExists = $connection->fetchOne(
                'SELECT id FROM payer WHERE id = :id AND is_active = true',
                ['id' => $payerId]
            );
            if (!$payerExists) {
                $this->addFlash('danger', 'El financiador seleccionado no existe o está inactivo.');
                return $this->render('admission/wizard/step2.html.twig', [
                    'wizard' => $wizardData,
                ]);
            }

            $agreementExists = $connection->fetchOne(
                'SELECT id FROM agreement WHERE id = :id AND is_active = true AND payer_id = :payer',
                ['id' => $agreementId, 'payer' => $payerId]
            );
            if (!$agreementExists) {
                $this->addFlash('danger', 'El convenio seleccionado no existe o no corresponde al financiador.');
                return $this->render('admission/wizard/step2.html.twig', [
                    'wizard' => $wizardData,
                ]);
            }

            $record->setPayerId($payerId);
            $record->setAgreementId($agreementId);
            $this->entityManager->flush();

            $wizardData['payer'] = (string) $payerId;
            $wizardData['agreement'] = (string) $agreementId;
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
        if (empty($wizardData['patient_id']) || empty($wizardData['admission_record_id'])) {
            return $this->redirectToRoute('app_admission_hospitalization_index');
        }

        /** @var AdmissionRecord|null $record */
        $record = $this->entityManager->find(AdmissionRecord::class, (int) $wizardData['admission_record_id']);
        if (!$record instanceof AdmissionRecord) {
            $this->addFlash('danger', 'No se encontró la admisión en curso.');
            return $this->redirectToRoute('app_admission_hospitalization_index');
        }

        if ($request->isMethod('POST')) {
            $serviceId = $request->request->getInt('service', 0);
            $bedId = $request->request->getInt('bed', 0);
            if ($serviceId <= 0 || $bedId <= 0) {
                $this->addFlash('danger', 'Debes seleccionar servicio y cama.');
                return $this->render('admission/wizard/step3.html.twig', [
                    'wizard' => $wizardData,
                ]);
            }

            $connection = $this->entityManager->getConnection();
            $serviceExists = $connection->fetchOne(
                'SELECT id FROM service WHERE id = :id AND is_active = true',
                ['id' => $serviceId]
            );
            if (!$serviceExists) {
                $this->addFlash('danger', 'El servicio seleccionado no existe o está inactivo.');
                return $this->render('admission/wizard/step3.html.twig', [
                    'wizard' => $wizardData,
                ]);
            }

            $bedExists = $connection->fetchOne(
                'SELECT id FROM bed WHERE id = :id AND is_active = true',
                ['id' => $bedId]
            );
            if (!$bedExists) {
                $this->addFlash('danger', 'La cama seleccionada no existe o está inactiva.');
                return $this->render('admission/wizard/step3.html.twig', [
                    'wizard' => $wizardData,
                ]);
            }

            $record->setServiceId($serviceId);
            $record->setBedId($bedId);
            $record->setStatus('completed');
            $this->entityManager->flush();

            $wizardData['service'] = (string) $serviceId;
            $wizardData['bed'] = (string) $bedId;
            $request->getSession()->set('admission_wizard', $wizardData);

            $admissionId = $record->getId();
            $request->getSession()->remove('admission_wizard');

            return $this->redirectToRoute('app_admission_view', [
                'id' => $admissionId,
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
