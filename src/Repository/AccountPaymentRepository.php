<?php

namespace App\Repository;

use App\Entity\Tenant\AccountPayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * AccountPaymentRepository - Repositorio para pagos de cuenta
 * Mock temporal hasta conectar base de datos Legacy
 */
class AccountPaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountPayment::class);
    }

    /**
     * GetCajeroByUser - Obtiene ID de cajero por usuario
     * Mock: Retorna ID 1
     */
    public function GetCajeroByUser(int $userId): int
    {
        // Mock: Cajero por defecto
        return 1;
    }

    /**
     * GetMascotasPorDuenoApi1 - Obtiene mascotas por dueño
     * Mock: Retorna array vacío
     */
    public function GetMascotasPorDuenoApi1(int $duenoId): array
    {
        // Mock: Sin mascotas
        return [];
    }

    /**
     * GetTratamientoGlosa - Obtiene glosa de tratamiento
     * Mock: Retorna null
     */
    public function GetTratamientoGlosa(int $tratamientoId)
    {
        // Mock: Sin tratamiento
        return null;
    }

    /**
     * GetPagosHistoricos - Obtiene pagos históricos del paciente
     * Mock: Retorna array vacío
     */
    public function GetPagosHistoricos(int $patientId, int $estadoActivo, bool $incluirAnulados = false): array
    {
        // Mock: Sin pagos históricos
        return [];
    }

    /**
     * GetPagosHistoricosApi1 - Obtiene pagos históricos (versión API 1)
     * Mock: Retorna array vacío
     */
    public function GetPagosHistoricosApi1(int $patientId, int $estadoActivo): array
    {
        // Mock: Sin pagos históricos
        return [];
    }

    /**
     * GetReservasInpagoHistoricos - Obtiene reservas impagas históricas
     * Mock: Retorna array vacío
     */
    public function GetReservasInpagoHistoricos(int $patientId, $entityManager): array
    {
        // Mock: Sin reservas impagas
        return [];
    }

    /**
     * GetReservasInpagoHistoricosApi1 - Obtiene reservas impagas (versión API 1)
     * Mock: Retorna array vacío
     */
    public function GetReservasInpagoHistoricosApi1(int $patientId, int $estadoActivo): array
    {
        // Mock: Sin reservas impagas
        return [];
    }

    /**
     * GetTratamientosHistoricos - Obtiene tratamientos históricos
     * Mock: Retorna array vacío
     */
    public function GetTratamientosHistoricos(int $patientId): array
    {
        // Mock: Sin tratamientos históricos
        return [];
    }

    /**
     * ObtenerDatosPagoGarantia - Obtiene datos de pago de garantía
     * Mock: Retorna null
     */
    public function ObtenerDatosPagoGarantia(int $pagoId)
    {
        // Mock: Sin datos de pago garantía
        return null;
    }

    /**
     * GetCajaByUser - Obtiene caja activa del usuario en una fecha
     * Mock: Retorna null (no hay caja abierta)
     */
    public function GetCajaByUser($userId, $fecha)
    {
        // Mock: Sin caja abierta para el usuario
        return null;
    }
}
