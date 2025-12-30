<?php

namespace App\Entity\Tenant;

use App\Repository\DocumentBatchRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentBatch - Talonario de documentos (facturas, boletas)
 * (Migrado desde Legacy Talonario)
 */
#[ORM\Entity(repositoryClass: DocumentBatchRepository::class)]
#[ORM\Table(name: 'document_batch')]
class DocumentBatch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $cashierStationId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $subCompanyId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $documentTypeId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $startNumber = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $endNumber = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $currentNumber = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $statusId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $stackStatusId = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCashierStationId(): ?int
    {
        return $this->cashierStationId;
    }

    public function setCashierStationId(?int $cashierStationId): self
    {
        $this->cashierStationId = $cashierStationId;
        return $this;
    }

    public function getSubCompanyId(): ?int
    {
        return $this->subCompanyId;
    }

    public function setSubCompanyId(?int $subCompanyId): self
    {
        $this->subCompanyId = $subCompanyId;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getIdSubEmpresa()
    {
        return $this->subCompanyId ? (object)['id' => $this->subCompanyId] : null;
    }

    public function getDocumentTypeId(): ?int
    {
        return $this->documentTypeId;
    }

    public function setDocumentTypeId(?int $documentTypeId): self
    {
        $this->documentTypeId = $documentTypeId;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getIdRelEmpresaTipoDocumento()
    {
        if (!$this->documentTypeId) return null;
        return (object)[
            'idTipoDocumento' => (object)['id' => $this->documentTypeId, 'getid' => fn() => $this->documentTypeId]
        ];
    }

    public function getStartNumber(): ?int
    {
        return $this->startNumber;
    }

    public function setStartNumber(?int $startNumber): self
    {
        $this->startNumber = $startNumber;
        return $this;
    }

    public function getEndNumber(): ?int
    {
        return $this->endNumber;
    }

    public function setEndNumber(?int $endNumber): self
    {
        $this->endNumber = $endNumber;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getnumeroTermino(): ?int
    {
        return $this->endNumber;
    }

    public function getCurrentNumber(): ?int
    {
        return $this->currentNumber;
    }

    public function setCurrentNumber(?int $currentNumber): self
    {
        $this->currentNumber = $currentNumber;
        return $this;
    }

    // Alias para compatibilidad Legacy
    public function getNumeroActual(): ?int
    {
        return $this->currentNumber;
    }

    public function getStatusId(): ?int
    {
        return $this->statusId;
    }

    public function setStatusId(?int $statusId): self
    {
        $this->statusId = $statusId;
        return $this;
    }

    public function getStackStatusId(): ?int
    {
        return $this->stackStatusId;
    }

    public function setStackStatusId(?int $stackStatusId): self
    {
        $this->stackStatusId = $stackStatusId;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }
}
