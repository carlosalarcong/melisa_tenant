<?php

declare(strict_types=1);

namespace App\Service\Dashboard;

use App\Entity\Tenant\Member;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Psr\Log\LoggerInterface;

/**
 * Servicio para obtener métricas del Dashboard
 */
class DashboardMetricsService
{
    public function __construct(
        private TenantEntityManager $em,
        private LoggerInterface $logger,
        private DashboardPermissionService $permissionService
    ) {}

    /**
     * Obtener todas las métricas del dashboard
     * 
     * @param array $tenant Datos del tenant desde TenantContext
     * @param array $userRoles Roles del usuario actual
     */
    public function getDashboardMetrics(array $tenant, array $userRoles = ['ROLE_USER']): array
    {
        try {
            $metrics = [];

            // Solo incluir métricas según permisos del rol
            if ($this->permissionService->canViewMetric('users', $userRoles)) {
                $metrics['users'] = $this->getUserMetrics();
            }

            if ($this->permissionService->canViewMetric('appointments', $userRoles)) {
                $metrics['appointments'] = $this->getAppointmentMetrics();
            }

            if ($this->permissionService->canViewMetric('revenue', $userRoles)) {
                $metrics['revenue'] = $this->getRevenueMetrics();
            }

            // Actividad y alertas siempre disponibles
            $metrics['activity'] = $this->getRecentActivity();
            $metrics['alerts'] = $this->getSystemAlerts($tenant, $userRoles);

            return $metrics;
        } catch (\Exception $e) {
            $this->logger->error('Error obteniendo métricas del dashboard', [
                'error' => $e->getMessage(),
                'tenant' => $tenant['id'] ?? 'unknown'
            ]);
            return $this->getDefaultMetrics();
        }
    }

    /**
     * Métricas de usuarios
     */
    private function getUserMetrics(): array
    {
        // Contar usuarios activos
        $activeUsers = (int) $this->em->createQueryBuilder()
            ->select('COUNT(m.id)')
            ->from(Member::class, 'm')
            ->where('m.isActive = :active')
            ->setParameter('active', true)
            ->getQuery()
            ->getSingleScalarResult();

        // Contar usuarios totales
        $totalUsers = (int) $this->em->createQueryBuilder()
            ->select('COUNT(m.id)')
            ->from(Member::class, 'm')
            ->getQuery()
            ->getSingleScalarResult();

        // Usuarios nuevos este mes (simulado)
        $newThisMonth = 5;
        
        return [
            'active' => $activeUsers,
            'total' => $totalUsers,
            'inactive' => $totalUsers - $activeUsers,
            'new_this_month' => $newThisMonth,
            'growth_percentage' => $totalUsers > 0 ? round(($newThisMonth / $totalUsers) * 100, 1) : 0,
        ];
    }

    /**
     * Métricas de citas (simulado - adaptar cuando exista la entidad)
     */
    private function getAppointmentMetrics(): array
    {
        // TODO: Implementar cuando exista entidad de Citas
        return [
            'today' => 23,
            'in_progress' => 5,
            'completed' => 15,
            'pending' => 3,
            'cancelled' => 2,
            'week_total' => 142,
            'trend' => '+12%', // vs semana anterior
        ];
    }

    /**
     * Métricas de ingresos (simulado - adaptar cuando exista la entidad)
     */
    private function getRevenueMetrics(): array
    {
        // TODO: Implementar cuando exista módulo de facturación/caja
        return [
            'today' => 450000,
            'week' => 2850000,
            'month' => 12500000,
            'currency' => 'CLP',
            'trend' => '+8.5%', // vs mes anterior
        ];
    }

    /**
     * Actividad reciente del sistema
     */
    private function getRecentActivity(): array
    {
        // TODO: Implementar con log de actividades o eventos del sistema
        return [
            [
                'type' => 'user_login',
                'icon' => 'fa-sign-in-alt',
                'color' => 'success',
                'message' => 'Usuario María López inició sesión',
                'time' => '5 min',
            ],
            [
                'type' => 'appointment_created',
                'icon' => 'fa-calendar-plus',
                'color' => 'primary',
                'message' => 'Nueva cita agendada para Juan Pérez',
                'time' => '15 min',
            ],
            [
                'type' => 'patient_registered',
                'icon' => 'fa-user-plus',
                'color' => 'info',
                'message' => 'Nuevo paciente registrado: Ana García',
                'time' => '1 hora',
            ],
            [
                'type' => 'payment_received',
                'icon' => 'fa-dollar-sign',
                'color' => 'success',
                'message' => 'Pago recibido: $85.000',
                'time' => '2 horas',
            ],
            [
                'type' => 'user_created',
                'icon' => 'fa-user-shield',
                'color' => 'warning',
                'message' => 'Nuevo usuario creado por admin',
                'time' => '3 horas',
            ],
        ];
    }

    /**
     * Alertas del sistema
     * 
     * @param array $tenant Datos del tenant
     * @param array $userRoles Roles del usuario
     */
    private function getSystemAlerts(array $tenant, array $userRoles = ['ROLE_USER']): array
    {
        $alerts = [];

        // Alertas de licencias solo para admin
        if ($this->permissionService->isAdmin($userRoles)) {
            // Verificar licencias disponibles
            $activeUsers = (int) $this->em->createQueryBuilder()
                ->select('COUNT(m.id)')
                ->from(Member::class, 'm')
                ->where('m.isActive = :active')
                ->setParameter('active', true)
                ->getQuery()
                ->getSingleScalarResult();

            // Simular límite de licencias
            $licenseLimit = 50; // TODO: Obtener de License entity
            $available = $licenseLimit - $activeUsers;

            if ($available <= 5) {
                $alerts[] = [
                    'type' => 'warning',
                    'icon' => 'fa-exclamation-triangle',
                    'title' => 'Licencias por agotarse',
                    'message' => "Solo quedan {$available} licencias disponibles de {$licenseLimit}",
                    'action_text' => 'Gestionar licencias',
                    'action_url' => '/admin/licenses',
                ];
            }
        }

        // Alertas para médicos
        if (in_array(DashboardPermissionService::ROLE_DOCTOR, $userRoles)) {
            // TODO: Agregar alertas específicas para médicos
            // - Resultados de laboratorio pendientes
            // - Citas sin confirmar
        }

        // Alertas para recepcionistas
        if (in_array(DashboardPermissionService::ROLE_RECEPTIONIST, $userRoles)) {
            // TODO: Agregar alertas específicas
            // - Citas sin check-in
            // - Pagos pendientes
        }

        // Si no hay alertas, mostrar mensaje de sistema OK
        if (empty($alerts)) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'fa-info-circle',
                'title' => 'Sistema funcionando correctamente',
                'message' => 'No hay alertas pendientes',
                'action_text' => null,
                'action_url' => null,
            ];
        }

        return $alerts;
    }

    /**
     * Métricas por defecto en caso de error
     */
    private function getDefaultMetrics(): array
    {
        return [
            'users' => [
                'active' => 0,
                'total' => 0,
                'inactive' => 0,
                'new_this_month' => 0,
                'growth_percentage' => 0,
            ],
            'appointments' => [
                'today' => 0,
                'in_progress' => 0,
                'completed' => 0,
                'pending' => 0,
                'cancelled' => 0,
                'week_total' => 0,
                'trend' => '0%',
            ],
            'revenue' => [
                'today' => 0,
                'week' => 0,
                'month' => 0,
                'currency' => 'CLP',
                'trend' => '0%',
            ],
            'activity' => [],
            'alerts' => [],
        ];
    }

    /**
     * Obtener módulos disponibles para el usuario según sus roles
     * 
     * @param array $userRoles Roles del usuario actual
     */
    public function getAvailableModules(array $userRoles = ['ROLE_USER']): array
    {
        $allModules = [
            [
                'id' => 'admin_users',
                'name' => 'Administración de Usuarios',
                'icon' => 'fa-users-cog',
                'color' => 'primary',
                'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'description' => 'Gestión de usuarios, roles y permisos',
                'url' => '/admin/users',
                'category' => 'admin',
                'featured' => true,
            ],
            [
                'id' => 'patients',
                'name' => 'Directorio de Pacientes',
                'icon' => 'fa-hospital-user',
                'color' => 'success',
                'gradient' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                'description' => 'Registro y gestión de pacientes',
                'url' => '/patients',
                'category' => 'clinical',
                'featured' => true,
            ],
            [
                'id' => 'appointments',
                'name' => 'Agenda',
                'icon' => 'fa-calendar-alt',
                'color' => 'info',
                'gradient' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                'description' => 'Gestión de citas y horarios',
                'url' => '/appointments',
                'category' => 'clinical',
                'featured' => true,
            ],
            [
                'id' => 'ehr',
                'name' => 'Registro Clínico Electrónico',
                'icon' => 'fa-file-medical',
                'color' => 'danger',
                'gradient' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                'description' => 'Fichas clínicas y atenciones',
                'url' => '/clinical-records',
                'category' => 'clinical',
                'featured' => true,
            ],
            [
                'id' => 'billing',
                'name' => 'Caja',
                'icon' => 'fa-cash-register',
                'color' => 'warning',
                'gradient' => 'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)',
                'description' => 'Gestión de pagos y facturación',
                'url' => '/billing',
                'category' => 'financial',
                'featured' => false,
            ],
            [
                'id' => 'reports',
                'name' => 'Informes',
                'icon' => 'fa-chart-bar',
                'color' => 'secondary',
                'gradient' => 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
                'description' => 'Reportes y estadísticas',
                'url' => '/reports',
                'category' => 'admin',
                'featured' => false,
            ],
            [
                'id' => 'maintenance',
                'name' => 'Mantenedores',
                'icon' => 'fa-database',
                'color' => 'dark',
                'gradient' => 'linear-gradient(135deg, #d299c2 0%, #fef9d7 100%)',
                'description' => 'Tablas maestras del sistema',
                'url' => '/maintenance',
                'category' => 'admin',
                'featured' => false,
            ],
            [
                'id' => 'config',
                'name' => 'Configuraciones',
                'icon' => 'fa-cog',
                'color' => 'secondary',
                'gradient' => 'linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%)',
                'description' => 'Configuración del sistema',
                'url' => '/settings',
                'category' => 'admin',
                'featured' => false,
            ],
            [
                'id' => 'pharmacy',
                'name' => 'Farmacia',
                'icon' => 'fa-pills',
                'color' => 'success',
                'gradient' => 'linear-gradient(135deg, #a1ffce 0%, #faffd1 100%)',
                'description' => 'Gestión de medicamentos y prescripciones',
                'url' => '/pharmacy',
                'category' => 'clinical',
                'featured' => false,
            ],
            [
                'id' => 'lab',
                'name' => 'Laboratorio',
                'icon' => 'fa-flask',
                'color' => 'info',
                'gradient' => 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
                'description' => 'Órdenes y resultados de laboratorio',
                'url' => '/laboratory',
                'category' => 'clinical',
                'featured' => false,
            ],
        ];

        // Filtrar módulos según permisos del usuario
        return array_filter($allModules, function($module) use ($userRoles) {
            return $this->permissionService->canAccessModule($module['id'], $userRoles);
        });
    }
}
