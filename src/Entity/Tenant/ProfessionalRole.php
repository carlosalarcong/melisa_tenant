<?php

namespace App\Entity\Tenant;

use App\Repository\ProfessionalRoleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProfessionalRole - Rol de profesional asignado a usuario
 * (Migrado desde Legacy RolProfesional)
 * HL7: STF-18 Job Code/Class (Provider Role)
 */
#[ORM\Entity(repositoryClass: ProfessionalRoleRepository::class)]
#[ORM\Table(name: 'professional_role')]
class ProfessionalRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $webComment = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $overbookingAmount = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $roleId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $userId = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWebComment(): ?string
    {
        return $this->webComment;
    }

    public function setWebComment(?string $webComment): self
    {
        $this->webComment = $webComment;
        return $this;
    }

    // Alias Legacy
    public function getComentarioWeb(): ?string
    {
        return $this->webComment;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    // Alias Legacy
    public function getComentario(): ?string
    {
        return $this->comment;
    }

    public function getOverbookingAmount(): ?int
    {
        return $this->overbookingAmount;
    }

    public function setOverbookingAmount(?int $overbookingAmount): self
    {
        $this->overbookingAmount = $overbookingAmount;
        return $this;
    }

    // Alias Legacy
    public function getCantidadSobrecupo(): ?int
    {
        return $this->overbookingAmount;
    }

    public function getRoleId(): ?int
    {
        return $this->roleId;
    }

    public function setRoleId(?int $roleId): self
    {
        $this->roleId = $roleId;
        return $this;
    }

    public function getIdRol()
    {
        return $this->roleId ? (object)['id' => $this->roleId] : null;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getIdUsuario()
    {
        return $this->userId ? (object)['id' => $this->userId] : null;
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

    public function getIdEstado()
    {
        return (object)['id' => $this->isActive ? 1 : 0];
    }
}
