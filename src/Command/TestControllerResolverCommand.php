<?php

namespace App\Command;

use App\Service\DynamicControllerResolver;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-controller-resolver',
    description: 'Prueba la resolución dinámica de controladores para un tenant específico'
)]
class TestControllerResolverCommand extends Command
{
    public function __construct(
        private DynamicControllerResolver $controllerResolver
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('tenant', InputArgument::REQUIRED, 'Subdomain del tenant a probar')
            ->addArgument('controller', InputArgument::OPTIONAL, 'Tipo de controlador a resolver', 'dashboard')
            ->addArgument('action', InputArgument::OPTIONAL, 'Acción del controlador', 'index');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $tenant = $input->getArgument('tenant');
        $controller = $input->getArgument('controller');
        $action = $input->getArgument('action');
        
        $io->title("🧪 Prueba de Resolución Dinámica de Controladores");
        
        $io->definitionList(
            ['Tenant' => $tenant],
            ['Controller' => $controller],
            ['Action' => $action]
        );
        
        try {
            // 1. Probar resolución de controlador
            $io->section('📋 Resolución de Controlador');
            $resolvedController = $this->controllerResolver->resolveController($tenant, $controller, $action);
            $io->success("Controlador resuelto: {$resolvedController}");
            
            // 2. Probar generación de ruta
            $io->section('🔗 Generación de Ruta');
            $redirectRoute = $this->controllerResolver->generateRedirectRoute($tenant, $controller);
            $io->success("Ruta generada: {$redirectRoute}");
            
            // 3. Información de debug
            $io->section('🔍 Información de Debug');
            $debugInfo = $this->controllerResolver->getDebugInfo($tenant);
            
            foreach ($debugInfo as $key => $value) {
                if (is_array($value)) {
                    $io->text("<info>{$key}:</info> " . implode(', ', $value));
                } else {
                    $io->text("<info>{$key}:</info> {$value}");
                }
            }
            
            // 4. Verificar si el controlador existe
            $io->section('✅ Verificación de Existencia');
            [$class, $method] = explode('::', $resolvedController);
            
            if (class_exists($class)) {
                $io->success("✅ Clase existe: {$class}");
                
                if (method_exists($class, $method)) {
                    $io->success("✅ Método existe: {$method}");
                } else {
                    $io->error("❌ Método no existe: {$method}");
                    return Command::FAILURE;
                }
            } else {
                $io->error("❌ Clase no existe: {$class}");
                return Command::FAILURE;
            }
            
            $io->success('🎉 ¡Resolución de controlador exitosa!');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $io->error('Error en la resolución: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}