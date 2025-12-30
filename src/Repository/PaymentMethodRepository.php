<?php

namespace App\Repository;

use App\Entity\Tenant\PaymentMethod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * PaymentMethodRepository - Repositorio para formas de pago
 * Mock temporal hasta conectar base de datos Legacy
 */
class PaymentMethodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentMethod::class);
    }

    /**
     * ObtieneFormaPago - Obtiene todas las formas de pago activas
     * Mock: Retorna formas de pago comunes
     */
    public function ObtieneFormaPago(): array
    {
        // Mock: Retornar formas de pago básicas
        return [
            ['id' => 1, 'nombre' => 'Efectivo'],
            ['id' => 2, 'nombre' => 'Débito'],
            ['id' => 3, 'nombre' => 'Crédito'],
            ['id' => 4, 'nombre' => 'Transferencia'],
        ];
    }

    /**
     * ListadoFormasDePagoParaMediosPago - Obtiene formas de pago principales
     * Mock: Retorna listado de medios de pago
     */
    public function ListadoFormasDePagoParaMediosPago(): array
    {
        // Mock: Efectivo, Débito, Crédito
        return [
            ['id' => 1, 'nombre' => 'Efectivo', 'codigo' => 'EF'],
            ['id' => 2, 'nombre' => 'Débito', 'codigo' => 'DB'],
            ['id' => 3, 'nombre' => 'Crédito', 'codigo' => 'CR'],
        ];
    }

    /**
     * ListadoFormasDePagoParaOtrosMedios - Obtiene otros medios de pago
     * Mock: Retorna otros métodos de pago
     */
    public function ListadoFormasDePagoParaOtrosMedios(): array
    {
        // Mock: Transferencia, Vale Vista, Cheque
        return [
            ['id' => 4, 'nombre' => 'Transferencia', 'codigo' => 'TF'],
            ['id' => 5, 'nombre' => 'Vale Vista', 'codigo' => 'VV'],
            ['id' => 6, 'nombre' => 'Cheque', 'codigo' => 'CH'],
        ];
    }

    /**
     * ObtieneBancoCaja - Obtiene bancos disponibles para caja
     * Mock: Retorna listado de bancos
     */
    public function ObtieneBancoCaja(int $estadoActivo, $empresa): array
    {
        // Mock: Bancos principales de Chile
        return [
            ['id' => 1, 'nombre' => 'Banco de Chile'],
            ['id' => 2, 'nombre' => 'Banco Estado'],
            ['id' => 3, 'nombre' => 'Santander'],
            ['id' => 4, 'nombre' => 'BCI'],
            ['id' => 5, 'nombre' => 'Scotiabank'],
        ];
    }

    /**
     * obtenerFormaPagoPadre - Obtiene formas de pago padre
     * Mock: Retorna formas de pago principales
     */
    public function obtenerFormaPagoPadre(): array
    {
        // Mock: Formas de pago raíz
        return [
            ['id' => 1, 'nombre' => 'Efectivo', 'esPadre' => true],
            ['id' => 2, 'nombre' => 'Banco', 'esPadre' => true],
        ];
    }
}
