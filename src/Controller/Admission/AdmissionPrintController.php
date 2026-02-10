<?php

namespace App\Controller\Admission;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Tenant\AdmissionRecord;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admission/print', name: 'app_admission_print_')]
class AdmissionPrintController extends AbstractTenantAwareController
{
    private const VALID_TYPES = ['pdf', 'html'];

    public function __construct(
        private TenantEntityManager $entityManager
    ) {}

    #[Route('/{id}/{type}', name: 'admission', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function admission(int $id, string $type): Response
    {
        if ($id <= 0) {
            throw $this->createNotFoundException('ID de admisión inválido');
        }

        if (!in_array($type, self::VALID_TYPES, true)) {
            return new Response(
                'Tipo de documento inválido. Valores permitidos: ' . implode(', ', self::VALID_TYPES),
                400
            );
        }

        /** @var AdmissionRecord|null $record */
        $record = $this->entityManager->find(AdmissionRecord::class, $id);
        if (!$record instanceof AdmissionRecord) {
            throw $this->createNotFoundException('Admisión no encontrada');
        }

        return $this->render('admission/print/admission_pdf.html.twig', [
            'admission_id' => $id,
            'document_type' => $type,
            'printed_at' => new \DateTimeImmutable(),
            'admission_record' => $record,
        ]);
    }

    #[Route('/urgency/{id}/{type}', name: 'urgency', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function urgency(int $id, string $type): Response
    {
        if ($id <= 0) {
            throw $this->createNotFoundException('ID de admisión inválido');
        }

        if (!in_array($type, self::VALID_TYPES, true)) {
            return new Response(
                'Tipo de documento inválido. Valores permitidos: ' . implode(', ', self::VALID_TYPES),
                400
            );
        }

        /** @var AdmissionRecord|null $record */
        $record = $this->entityManager->find(AdmissionRecord::class, $id);
        if (!$record instanceof AdmissionRecord || $record->getAdmissionType() !== 'urgencia') {
            throw $this->createNotFoundException('Admisión urgencia no encontrada');
        }

        return $this->render('admission/print/urgency_pdf.html.twig', [
            'admission_id' => $id,
            'document_type' => $type,
            'printed_at' => new \DateTimeImmutable(),
            'admission_record' => $record,
        ]);
    }
}
