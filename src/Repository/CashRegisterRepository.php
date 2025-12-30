<?php

namespace App\Repository;

use App\Entity\Tenant\CashRegister;
use App\Entity\Tenant\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * CashRegisterRepository - Repositorio para cajas de recaudación
 * Mock temporal hasta conectar base de datos Legacy
 */
class CashRegisterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CashRegister::class);
    }

    /**
     * findBy - Busca cajas por usuario
     * Mock: Retorna array vacío (no hay cajas abiertas)
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        // Mock: No hay cajas en estado de reapertura
        return [];
    }

    /**
     * GetSucursalAperturaCaja - Obtiene sucursal para apertura de caja
     * Mock: Retorna sucursal por defecto
     */
    public function GetSucursalAperturaCaja(int $userId)
    {
        // Mock: Retorna sucursal principal
        return (object)[
            'id' => 1,
            'nombre' => 'Sucursal Principal'
        ];
    }

    /**
     * obtenerMontoCierreCaja - Obtiene monto de cierre de caja por tipo
     * Mock: Retorna 0
     */
    public function obtenerMontoCierreCaja(int $cajaId, int $tipoId, int $estadoPago): float
    {
        // Mock: Sin montos en caja
        return 0.0;
    }

    /**
     * ObtenerItemsPagosCuentaCorrienteEmpresa - Obtiene pagos de cuenta corriente
     * Mock: Retorna array vacío
     */
    public function ObtenerItemsPagosCuentaCorrienteEmpresa(
        int $formaPagoId,
        int $cajaId,
        int $estadoId,
        int $empresaId
    ): array {
        // Mock: Sin pagos en cuenta corriente
        return [];
    }

    /**
     * GetBancoIfCuentaCorriente - Obtiene banco si es cuenta corriente
     * Mock: Retorna null
     */
    public function GetBancoIfCuentaCorriente(int $cajaId, int $formaPagoId)
    {
        // Mock: Sin banco asociado
        return null;
    }

    /**
     * GetCheques - Obtiene cheques de una caja
     * Mock: Retorna array vacío
     */
    public function GetCheques(int $cajaId, int $formaPagoId): array
    {
        // Mock: Sin cheques
        return [];
    }

    /**
     * GetCajasInforme - Obtiene cajas para informe
     * Mock: Retorna array vacío
     */
    public function GetCajasInforme(int $cajaId): array
    {
        // Mock: Sin cajas en informe
        return [];
    }

    /**
     * GetCajeroInformeCajas - Obtiene información del cajero
     * Mock: Retorna array vacío
     */
    public function GetCajeroInformeCajas(int $cajaId): array
    {
        // Mock: Sin información de cajero
        return [];
    }

    /**
     * GetCajasGarantias - Obtiene cajas con garantías
     * Mock: Retorna array vacío
     */
    public function GetCajasGarantias(int $empresaId): array
    {
        // Mock: Sin garantías
        return [];
    }

    /**
     * GetNumeroActualSinAnulacionTalonario - Obtiene número actual de talonario sin anulaciones
     * Mock: Retorna array vacío
     */
    public function GetNumeroActualSinAnulacionTalonario(
        array $talonarioIds,
        int $estadoAnulada,
        $entityManager
    ): array {
        // Mock: Sin talonarios
        return [];
    }

    /**
     * SubEmpresaDesdeCaja - Valida subempresa desde caja
     * Mock: Retorna false (no requiere validación)
     */
    public function SubEmpresaDesdeCaja(array $talonarios, array $prestaciones): bool
    {
        // Mock: No requiere validación de subempresa
        return false;
    }

    /**
     * GetInformacionDetalladaCaja - Obtiene información detallada de caja principal
     * Mock: Retorna null
     */
    public function GetInformacionDetalladaCaja(int $cajaId, int $userId, int $folio)
    {
        // Mock: Sin caja principal
        return null;
    }

    /**
     * GetInformacionDetalladaCajaSecundaria - Obtiene información detallada de caja secundaria
     * Mock: Retorna null
     */
    public function GetInformacionDetalladaCajaSecundaria(
        int $cajaId,
        int $userId,
        int $estadoBoleta,
        int $estadoPago,
        int $folio
    ) {
        // Mock: Sin caja secundaria
        return null;
    }
}
