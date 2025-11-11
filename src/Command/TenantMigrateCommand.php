<?php

namespace App\Command;

use Hakam\MultiTenancyBundle\Command\MigrationsMigrateCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Wrapper del comando tenant:migrations:migrate del bundle
 * 
 * Mantiene compatibilidad con app:migrate-tenant pero usa el bundle por debajo.
 * Para features avanzadas del comando original, ver MigrateTenantCommand (legacy).
 */
#[AsCommand(
    name: 'app:tenant:migrate',
    description: 'Migrar tenants usando el bundle (wrapper de tenant:migrations:migrate)',
    aliases: ['tenant:migrate']
)]
class TenantMigrateCommand extends MigrationsMigrateCommand
{
    protected function configure(): void
    {
        parent::configure();
        
        $this
            ->setHelp('
Este comando es un wrapper del comando tenant:migrations:migrate del bundle hakam/multi-tenancy-bundle.

<info>Uso básico:</info>

  <comment># Migrar todos los tenants</comment>
  php bin/console app:tenant:migrate update --all

  <comment># Migrar un tenant específico por ID</comment>
  php bin/console app:tenant:migrate update --dbid=3

  <comment># Migrar un tenant específico por subdomain (usando CustomTenantConfigProvider)</comment>
  php bin/console app:tenant:migrate update --dbid=melisalacolina

<info>Opciones del bundle:</info>

  --all              Migrar todos los tenants
  --dbid=ID          ID o subdomain del tenant a migrar
  --dry-run          Mostrar qué se ejecutaría sin hacer cambios
  --no-interaction   No pedir confirmación

<info>Comando original (legacy):</info>

  Si necesitas features avanzadas del comando original (cleanup, generate-only, etc),
  usa el comando legacy:
  
  php bin/console app:migrate-tenant [opciones]

<info>Diferencias principales:</info>

  Bundle (tenant:migrations:migrate):
  ✅ Usa TenantEntityManager
  ✅ Usa SwitchDbEvent
  ✅ Integrado con el ecosistema del bundle
  
  Legacy (app:migrate-tenant):
  ✅ Cleanup de tablas duplicadas
  ✅ Cleanup de referencias huérfanas
  ✅ Auto-detección de tenants
  ✅ Reportes detallados
            ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Delegar al comando padre del bundle
        return parent::execute($input, $output);
    }
}
