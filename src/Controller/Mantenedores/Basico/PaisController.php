<?php

namespace App\Controller\Mantenedores\Basico;

use App\Service\Basico\PaisService;
use App\Service\TenantContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class PaisController extends AbstractController
{
    private PaisService $paisService;
    private TenantContext $tenantContext;
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        PaisService $paisService,
        TenantContext $tenantContext,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->paisService = $paisService;
        $this->tenantContext = $tenantContext;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * Vista AJAX para cargar solo el contenido del mantenedor
     */
    public function content(): Response
    {
        try {
            $paises = $this->paisService->getAllPaises();
            $tenant = $this->getCurrentTenant();
            
            return $this->render('mantenedores/basico/pais/content.html.twig', [
                'mantenedor_config' => $this->getMantenedorConfig(),
                'tenant' => $tenant,
                'paises' => $paises,
                'csrf_token' => $this->csrfTokenManager->getToken('mantenedor_form')->getValue()
            ]);
        } catch (\Exception $e) {
            return $this->render('mantenedores/basico/pais/content.html.twig', [
                'mantenedor_config' => $this->getMantenedorConfig(),
                'tenant' => $this->getCurrentTenant(),
                'paises' => [],
                'error' => $e->getMessage(),
                'csrf_token' => $this->csrfTokenManager->getToken('mantenedor_form')->getValue()
            ]);
        }
    }

    private function getMantenedorConfig(): array
    {
        return [
            'title' => 'Gestión de Países',
            'entity_name' => 'País',
            'entity_name_plural' => 'Países'
        ];
    }

    private function getCurrentTenant(): array
    {
        // Por ahora, hardcodeado para melisahospital
        return [
            'name' => 'Melisa Hospital',
            'subdomain' => 'melisahospital'
        ];
    }
    
    /**
     * Crear nuevo país (POST)
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $data = $this->extractRequestData($request);
            $pais = $this->paisService->createPais($data);
            
            return new JsonResponse([
                'success' => true,
                'message' => 'País creado exitosamente',
                'data' => $pais
            ]);
            
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Obtener país por ID (GET)
     */
    public function show(int $id): JsonResponse
    {
        try {
            $pais = $this->paisService->getPaisById($id);
            
            if (!$pais) {
                return new JsonResponse(['error' => 'País no encontrado'], 404);
            }
            
            return new JsonResponse([
                'success' => true,
                'data' => $pais
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Actualizar país existente (PUT/PATCH)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $data = $this->extractRequestData($request);
            $pais = $this->paisService->updatePais($id, $data);
            
            return new JsonResponse([
                'success' => true,
                'message' => 'País actualizado exitosamente',
                'data' => $pais
            ]);
            
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getMessage() === 'País no encontrado' ? 404 : 400);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error interno del servidor'], 500);
        }
    }
    
    /**
     * Eliminar país (DELETE)
     */
    public function delete(Request $request, int $id): JsonResponse
    {
        try {
            $nombrePais = $this->paisService->deletePais($id);
            
            return new JsonResponse([
                'success' => true,
                'message' => "País \"$nombrePais\" eliminado exitosamente"
            ]);
            
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Extrae datos del request (JSON o form data)
     */
    private function extractRequestData(Request $request): array
    {
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            // Si no hay JSON, obtener de form data
            $data = [
                'nombrePais' => $request->get('nombrePais'),
                'nombreGentilicio' => $request->get('nombreGentilicio'),
                'activo' => $request->get('activo') === 'on' || $request->get('activo') === '1' || $request->get('activo') === true
            ];
        }
        
        return $data;
    }
}