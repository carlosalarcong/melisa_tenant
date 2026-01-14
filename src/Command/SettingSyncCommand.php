<?php

namespace App\Command;


use App\Entity\Main\TenantDb;
use App\Entity\Tenant\Setting;
use Doctrine\ORM\EntityManagerInterface;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Hakam\MultiTenancyBundle\Event\SwitchDbEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;
use function Symfony\Component\Clock\now;

#[AsCommand(
    name: 'app:SettingSyncCommand',
    description: 'Setting Sync Tenant',
    hidden: false
)]
class SettingSyncCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $mainEntityManager,
        private EventDispatcherInterface $dispatcher,
        private TenantEntityManager $tenantEntityManager
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDefinition(
                new InputDefinition([
                    new InputArgument(
                        'environment',
                        InputArgument::REQUIRED,
                        'The base database_name to compare with yaml.'
                    ),
                    new InputOption(
                        'dump-sql', null, InputOption::VALUE_NONE,
                        'Dumps the generated SQL statements to the screen (does not execute them).'
                    ),
                    new InputOption(
                        'pretty', null, InputOption::VALUE_NONE,
                        'Dumps the generated SQL statements in pretty format.'
                    ),
                    new InputOption(
                        'stats', null, InputOption::VALUE_NONE,
                        'Show info about generated querys.'
                    ),
                ])
            )
            ->setDescription('Dumps the SQL needed to update the database settings table to match the current mapping metadata. This scripts require data consistency in all databases')
            ->setHelp(
                <<<EOT
                El comando <info>deploy:settings:sync</info> genera los SQLs necesarios para
sincronizar los settings de todas las bases tomando como base la base de datos especificada.

Por ejemplo, para tomar como base la base de datos prod_demo:

<info>php app/console deploy:settings:sync --dump-sql prod_demo</info>

EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $environment = $input->getArgument('environment');
        $parts = explode('.', $environment);

        $yaml = new Parser();
        $config = $yaml->parse(file_get_contents(__DIR__ . '/../../config/settings.yml'));

        $flatYamlSettings= [];
        $flatSettings= [];

        // empty file
        if ($config === null) {
            $config = array();
        }
        if (!is_array($config)) {
            $output->writeln("Archivo de settings inválido.");
            return Command::FAILURE;
        }

        //traer el switch connection y buscar todas las instancias activas
        if (count($parts) >= 1) {
            $tenantUser = $this->mainEntityManager->getRepository(TenantDb::class)->findOneBy(['slug' => $parts[0], 'active' => true]);
            $switchEvent = new SwitchDbEvent($tenantUser->getSlug());
            $this->dispatcher->dispatch($switchEvent);
        }

        $allSettings = $this->tenantEntityManager->getRepository(Setting::class)->findAll();

        foreach($allSettings as $set) {
            $flatSettings[] = $set->getSlug();
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($config),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        $insert_count = 0;
        $yaml_count = 0;
        $insert = '';
        $ender = PHP_EOL;
        foreach($iterator as $key => $val) {
            if (gettype($val) == "array" && array_key_exists('field_format', $val)) {
                $flatYamlSettings[] = $key;
                if (!in_array($key, $flatSettings)) {
                    $value = $val['field_default_value'];
                    $field_format = $val['field_format'];
                    //prepare tinyint field_required
                    $_required = isset($val['field_required']) ? 1 : 0;
                    $field_required = $_required == 1 && $val['field_required'] == 1 ? 1 : 0;

                    if (gettype($value) == "array") $value = serialize($value);
                    else $value = (string) $value;

                    $setting = new Setting();
                    $setting->setName($key);
                    $setting->setSlug($key);
                    $setting->setFieldDefaultValue($value);
                    $setting->setFieldFormat($field_format);
                    $setting->setFieldRequired($field_required);
                    $setting->setCreatedAt(now());
                    $setting->setUpdatedAt(now());
                    $this->tenantEntityManager->persist($setting);
                    $this->tenantEntityManager->flush();
                    $insert.= "INSERT INTO setting (slug, field_default_value, field_format, field_required, created_at, updated_at) values ('$key', '$value', '$field_format', $field_required, now(), now());" . $ender;

                    $insert_count++;
                }
                $yaml_count++;
            }
        }

        $delete = "";
        $delete_count = 0;
        foreach ($flatSettings as $dbSetting) {
            if (!in_array($dbSetting, $flatYamlSettings)) {
                $setting = $this->tenantEntityManager->getRepository(Setting::class)->findOneBy(['slug' => $dbSetting]);
                $this->tenantEntityManager->remove($setting);
                $this->tenantEntityManager->flush();
                $delete.= "DELETE FROM setting WHERE slug = '$dbSetting';" . $ender;
                $delete_count++;
            }
        }

        $output->writeln("-- PRESQL: $insert_count INSERT QUERYS encontradas");
        $output->writeln($insert);
        $output->writeln("-- POSTSQL: $delete_count DELETE QUERYS encontradas");
        $output->writeln($delete);

        return Command::SUCCESS;

    }
}