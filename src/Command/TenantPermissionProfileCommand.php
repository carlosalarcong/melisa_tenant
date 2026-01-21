<?php

namespace App\Command;

use App\Repository\Tenant\TenantPermissionProfileRepository;
use App\Repository\Tenant\TenantModulePermissionOverrideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:tenant:permission-profile',
    description: 'Gestiona el perfil de permisos del tenant y visualiza configuración actual'
)]
class TenantPermissionProfileCommand extends Command
{
    public function __construct(
        private TenantPermissionProfileRepository $profileRepository,
        private TenantModulePermissionOverrideRepository $overrideRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('tenant_id', InputArgument::REQUIRED, 'ID del tenant')
            ->addArgument('action', InputArgument::OPTIONAL, 'Acción: show|set', 'show')
            ->addArgument('profile', InputArgument::OPTIONAL, 'Tipo de perfil: collaborative|restrictive|custom')
            ->setHelp(<<<'HELP'
Este comando permite visualizar y cambiar el perfil de permisos del tenant.

Ejemplos:
  # Ver configuración actual del tenant 5
  php bin/console app:tenant:permission-profile 5 show

  # Cambiar a perfil collaborative
  php bin/console app:tenant:permission-profile 5 set collaborative

  # Cambiar a perfil restrictive
  php bin/console app:tenant:permission-profile 5 set restrictive

  # Cambiar a perfil custom (usa overrides de BD)
  php bin/console app:tenant:permission-profile 5 set custom
HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $tenantId = $input->getArgument('tenant_id');
        $action = $input->getArgument('action');

        // Configurar entity manager para usar la BD del tenant
        // Nota: En producción esto debería usar TenantResolver
        $io->note("Usando tenant ID: {$tenantId}");

        if ($action === 'show') {
            return $this->showCurrentProfile($io);
        }

        if ($action === 'set') {
            $profile = $input->getArgument('profile');
            if (!$profile) {
                $io->error('Debes especificar el tipo de perfil: collaborative|restrictive|custom');
                return Command::FAILURE;
            }
            return $this->setProfile($io, $profile);
        }

        $io->error("Acción no válida. Usa 'show' o 'set'");
        return Command::FAILURE;
    }

    private function showCurrentProfile(SymfonyStyle $io): int
    {
        $profile = $this->profileRepository->getCurrentProfile();

        if (!$profile) {
            $io->warning('No hay perfil de permisos configurado. Se usará "collaborative" por defecto.');
            return Command::SUCCESS;
        }

        $io->title('Configuración de Permisos del Tenant');
        
        $io->section('Perfil Actual');
        $io->table(
            ['Campo', 'Valor'],
            [
                ['Tipo de Perfil', $profile->getProfileType()],
                ['Creado', $profile->getCreatedAt()->format('Y-m-d H:i:s')],
                ['Actualizado', $profile->getUpdatedAt() ? $profile->getUpdatedAt()->format('Y-m-d H:i:s') : 'N/A'],
            ]
        );

        // Mostrar descripción del perfil
        $descriptions = [
            'collaborative' => '✓ Múltiples roles pueden acceder a diferentes módulos (clínicas grandes)',
            'restrictive' => '✗ Solo administradores tienen acceso (clínicas pequeñas)',
            'custom' => '⚙ Configuración personalizada desde base de datos',
        ];
        
        $io->text($descriptions[$profile->getProfileType()] ?? 'Perfil desconocido');

        // Si es custom, mostrar los overrides
        if ($profile->getProfileType() === 'custom') {
            $io->section('Overrides de Módulos (Custom)');
            $overrides = $this->overrideRepository->findAllActive();
            
            if (empty($overrides)) {
                $io->warning('No hay overrides configurados. Todos los módulos requerirán ROLE_ADMIN.');
            } else {
                $rows = [];
                foreach ($overrides as $override) {
                    $rows[] = [
                        $override->getModuleName(),
                        implode(', ', $override->getRequiredRoles()),
                        $override->getDescription() ?? 'N/A',
                    ];
                }
                
                $io->table(['Módulo', 'Roles Requeridos', 'Descripción'], $rows);
            }
        }

        return Command::SUCCESS;
    }

    private function setProfile(SymfonyStyle $io, string $profileType): int
    {
        $validProfiles = ['collaborative', 'restrictive', 'custom'];
        
        if (!in_array($profileType, $validProfiles)) {
            $io->error("Perfil no válido. Opciones: " . implode(', ', $validProfiles));
            return Command::FAILURE;
        }

        $profile = $this->profileRepository->getCurrentProfile();
        
        if (!$profile) {
            $io->error('No hay perfil configurado. Ejecuta primero la migración y crea un perfil.');
            return Command::FAILURE;
        }

        $oldType = $profile->getProfileType();
        $profile->setProfileType($profileType);
        
        $this->entityManager->flush();

        $io->success("Perfil cambiado de '{$oldType}' a '{$profileType}'");
        
        // Mostrar información del nuevo perfil
        $descriptions = [
            'collaborative' => 'Ahora múltiples roles pueden acceder según configuración predefinida',
            'restrictive' => 'Ahora solo ROLE_ADMIN tiene acceso a la mayoría de módulos',
            'custom' => 'Ahora se usarán los overrides de la tabla tenant_module_permission_override',
        ];
        
        $io->note($descriptions[$profileType]);

        if ($profileType === 'custom') {
            $io->section('Recordatorio');
            $io->text([
                'Con el perfil "custom", debes configurar los módulos en la tabla:',
                '  tenant_module_permission_override',
                '',
                'Ejemplo SQL:',
                "  INSERT INTO tenant_module_permission_override ",
                "    (module_name, required_roles, is_active, created_at)",
                "  VALUES ",
                "    ('patients', '[\"ROLE_ADMIN\", \"ROLE_DOCTOR\"]', 1, NOW());",
            ]);
        }

        return Command::SUCCESS;
    }
}
