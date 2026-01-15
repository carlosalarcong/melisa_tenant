<?php

declare(strict_types=1);

namespace App\Service\Dashboard;

use Psr\Log\LoggerInterface;

/**
 * Servicio para gestionar permisos de módulos del dashboard por rol
 */
class DashboardPermissionService
{
    // Definición de roles del sistema
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_DOCTOR = 'ROLE_DOCTOR';
    public const ROLE_RECEPTIONIST = 'ROLE_RECEPTIONIST';
    public const ROLE_ACCOUNTANT = 'ROLE_ACCOUNTANT';
    public const ROLE_NURSE = 'ROLE_NURSE';
    public const ROLE_USER = 'ROLE_USER';

    // Permisos de módulos
    private array $modulePermissions = [
        'admin_users' => [
            self::ROLE_ADMIN,
        ],
        'patients' => [
            self::ROLE_ADMIN,
            self::ROLE_DOCTOR,
            self::ROLE_RECEPTIONIST,
            self::ROLE_NURSE,
        ],
        'appointments' => [
            self::ROLE_ADMIN,
            self::ROLE_DOCTOR,
            self::ROLE_RECEPTIONIST,
        ],
        'ehr' => [ // Registro Clínico Electrónico
            self::ROLE_ADMIN,
            self::ROLE_DOCTOR,
            self::ROLE_NURSE,
        ],
        'billing' => [
            self::ROLE_ADMIN,
            self::ROLE_RECEPTIONIST,
            self::ROLE_ACCOUNTANT,
        ],
        'reports' => [
            self::ROLE_ADMIN,
            self::ROLE_ACCOUNTANT,
            self::ROLE_DOCTOR, // Solo sus reportes
        ],
        'maintenance' => [
            self::ROLE_ADMIN,
        ],
        'config' => [
            self::ROLE_ADMIN,
        ],
        'pharmacy' => [
            self::ROLE_ADMIN,
            self::ROLE_DOCTOR,
            self::ROLE_NURSE,
        ],
        'lab' => [
            self::ROLE_ADMIN,
            self::ROLE_DOCTOR,
        ],
    ];

    // Permisos de métricas (qué métricas puede ver cada rol)
    private array $metricsPermissions = [
        'users' => [self::ROLE_ADMIN],
        'appointments' => [
            self::ROLE_ADMIN,
            self::ROLE_DOCTOR,
            self::ROLE_RECEPTIONIST,
        ],
        'revenue' => [
            self::ROLE_ADMIN,
            self::ROLE_ACCOUNTANT,
        ],
        'patients_count' => [
            self::ROLE_ADMIN,
            self::ROLE_DOCTOR,
            self::ROLE_RECEPTIONIST,
        ],
    ];

    public function __construct(
        private LoggerInterface $logger
    ) {}

    /**
     * Verifica si un usuario con roles específicos puede ver un módulo
     */
    public function canAccessModule(string $moduleId, array $userRoles): bool
    {
        // Admin puede ver todo
        if (in_array(self::ROLE_ADMIN, $userRoles)) {
            return true;
        }

        // Si el módulo no está definido, denegar por defecto
        if (!isset($this->modulePermissions[$moduleId])) {
            $this->logger->warning('Módulo no definido en permisos', [
                'module' => $moduleId
            ]);
            return false;
        }

        // Verificar si alguno de los roles del usuario tiene permiso
        $allowedRoles = $this->modulePermissions[$moduleId];
        return !empty(array_intersect($userRoles, $allowedRoles));
    }

    /**
     * Verifica si un usuario puede ver una métrica específica
     */
    public function canViewMetric(string $metricId, array $userRoles): bool
    {
        // Admin puede ver todo
        if (in_array(self::ROLE_ADMIN, $userRoles)) {
            return true;
        }

        if (!isset($this->metricsPermissions[$metricId])) {
            return false;
        }

        $allowedRoles = $this->metricsPermissions[$metricId];
        return !empty(array_intersect($userRoles, $allowedRoles));
    }

    /**
     * Obtiene todos los módulos disponibles para un usuario según sus roles
     */
    public function getAccessibleModules(array $userRoles): array
    {
        $accessibleModules = [];

        foreach (array_keys($this->modulePermissions) as $moduleId) {
            if ($this->canAccessModule($moduleId, $userRoles)) {
                $accessibleModules[] = $moduleId;
            }
        }

        return $accessibleModules;
    }

    /**
     * Obtiene las métricas visibles para un usuario según sus roles
     */
    public function getVisibleMetrics(array $userRoles): array
    {
        $visibleMetrics = [];

        foreach (array_keys($this->metricsPermissions) as $metricId) {
            if ($this->canViewMetric($metricId, $userRoles)) {
                $visibleMetrics[] = $metricId;
            }
        }

        return $visibleMetrics;
    }

    /**
     * Obtiene el rol principal del usuario (el de mayor jerarquía)
     */
    public function getPrimaryRole(array $userRoles): string
    {
        // Jerarquía de roles
        $hierarchy = [
            self::ROLE_ADMIN => 100,
            self::ROLE_ACCOUNTANT => 50,
            self::ROLE_DOCTOR => 40,
            self::ROLE_NURSE => 30,
            self::ROLE_RECEPTIONIST => 20,
            self::ROLE_USER => 10,
        ];

        $primaryRole = self::ROLE_USER;
        $highestPriority = 0;

        foreach ($userRoles as $role) {
            $priority = $hierarchy[$role] ?? 0;
            if ($priority > $highestPriority) {
                $highestPriority = $priority;
                $primaryRole = $role;
            }
        }

        return $primaryRole;
    }

    /**
     * Obtiene el nombre amigable del rol
     */
    public function getRoleName(string $role): string
    {
        $roleNames = [
            self::ROLE_ADMIN => 'Administrador',
            self::ROLE_DOCTOR => 'Médico',
            self::ROLE_RECEPTIONIST => 'Recepcionista',
            self::ROLE_ACCOUNTANT => 'Contador',
            self::ROLE_NURSE => 'Enfermera',
            self::ROLE_USER => 'Usuario',
        ];

        return $roleNames[$role] ?? 'Usuario';
    }

    /**
     * Verifica si el usuario tiene permisos de administrador
     */
    public function isAdmin(array $userRoles): bool
    {
        return in_array(self::ROLE_ADMIN, $userRoles);
    }

    /**
     * Obtiene acciones rápidas según el rol
     */
    public function getQuickActionsByRole(array $userRoles): array
    {
        $primaryRole = $this->getPrimaryRole($userRoles);

        $actionsByRole = [
            self::ROLE_ADMIN => [
                ['id' => 'new_user', 'label' => 'Nuevo Usuario', 'icon' => 'fa-user-plus', 'color' => 'primary'],
                ['id' => 'new_patient', 'label' => 'Nuevo Paciente', 'icon' => 'fa-hospital-user', 'color' => 'success'],
                ['id' => 'reports', 'label' => 'Ver Reportes', 'icon' => 'fa-chart-bar', 'color' => 'info'],
                ['id' => 'settings', 'label' => 'Configuración', 'icon' => 'fa-cog', 'color' => 'secondary'],
            ],
            self::ROLE_DOCTOR => [
                ['id' => 'new_consultation', 'label' => 'Nueva Consulta', 'icon' => 'fa-stethoscope', 'color' => 'primary'],
                ['id' => 'my_agenda', 'label' => 'Mi Agenda', 'icon' => 'fa-calendar-alt', 'color' => 'info'],
                ['id' => 'search_patient', 'label' => 'Buscar Paciente', 'icon' => 'fa-search', 'color' => 'success'],
                ['id' => 'pending_labs', 'label' => 'Resultados Pendientes', 'icon' => 'fa-flask', 'color' => 'warning'],
            ],
            self::ROLE_RECEPTIONIST => [
                ['id' => 'new_appointment', 'label' => 'Agendar Cita', 'icon' => 'fa-calendar-plus', 'color' => 'primary'],
                ['id' => 'new_patient', 'label' => 'Nuevo Paciente', 'icon' => 'fa-user-plus', 'color' => 'success'],
                ['id' => 'search_patient', 'label' => 'Buscar Paciente', 'icon' => 'fa-search', 'color' => 'info'],
                ['id' => 'cash_register', 'label' => 'Caja', 'icon' => 'fa-cash-register', 'color' => 'warning'],
            ],
            self::ROLE_ACCOUNTANT => [
                ['id' => 'daily_report', 'label' => 'Reporte Diario', 'icon' => 'fa-chart-line', 'color' => 'primary'],
                ['id' => 'pending_payments', 'label' => 'Pagos Pendientes', 'icon' => 'fa-money-check-alt', 'color' => 'warning'],
                ['id' => 'invoicing', 'label' => 'Facturación', 'icon' => 'fa-file-invoice-dollar', 'color' => 'success'],
                ['id' => 'expense_report', 'label' => 'Gastos', 'icon' => 'fa-receipt', 'color' => 'info'],
            ],
            self::ROLE_NURSE => [
                ['id' => 'patient_check', 'label' => 'Tomar Signos Vitales', 'icon' => 'fa-heartbeat', 'color' => 'danger'],
                ['id' => 'appointment_list', 'label' => 'Lista de Citas', 'icon' => 'fa-list', 'color' => 'primary'],
                ['id' => 'medication', 'label' => 'Medicación', 'icon' => 'fa-pills', 'color' => 'success'],
                ['id' => 'lab_orders', 'label' => 'Órdenes Lab', 'icon' => 'fa-vial', 'color' => 'info'],
            ],
        ];

        return $actionsByRole[$primaryRole] ?? $actionsByRole[self::ROLE_USER] ?? [];
    }

    /**
     * Obtiene widgets personalizados según el rol
     */
    public function getWidgetsByRole(array $userRoles): array
    {
        $primaryRole = $this->getPrimaryRole($userRoles);

        $widgetsByRole = [
            self::ROLE_ADMIN => ['users_stats', 'revenue_chart', 'appointments_overview', 'system_alerts'],
            self::ROLE_DOCTOR => ['my_appointments', 'pending_consultations', 'my_patients', 'recent_activity'],
            self::ROLE_RECEPTIONIST => ['today_appointments', 'pending_checkins', 'recent_patients', 'cash_summary'],
            self::ROLE_ACCOUNTANT => ['revenue_chart', 'pending_invoices', 'expense_summary', 'monthly_report'],
            self::ROLE_NURSE => ['pending_vitals', 'medication_schedule', 'today_patients', 'lab_results'],
        ];

        return $widgetsByRole[$primaryRole] ?? [];
    }
}
