<?php

namespace App\Controller\Admin;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Tenant\MenuItem;
use App\Repository\Tenant\MenuItemRepository;
use App\Service\Menu\MenuDefinition;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/menu-config', name: 'admin_menu_config_')]
class MenuConfigController extends AbstractTenantAwareController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MenuItemRepository $menuRepository,
        private MenuDefinition $menuDefinition
    ) {}

    /**
     * Lista todos los items del menú para administración
     */
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $menuItems = $this->menuRepository->getAllForAdmin();

        return $this->render('admin/menu_config/index.html.twig', [
            'menu_items' => $menuItems,
            'tenant_name' => $this->getTenantName(),
        ]);
    }

    /**
     * Formulario para crear nuevo item del menú
     */
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $menuItem = new MenuItem();
        
        if ($request->isMethod('POST')) {
            $this->processForm($menuItem, $request);
            
            $this->entityManager->persist($menuItem);
            $this->entityManager->flush();
            
            // Invalidar caché
            $this->menuDefinition->invalidateCache($this->getTenantId($request));
            
            $this->addFlash('success', 'Item del menú creado exitosamente');
            return $this->redirectToRoute('admin_menu_config_index');
        }

        $allItems = $this->menuRepository->getAllForAdmin();
        
        return $this->render('admin/menu_config/new.html.twig', [
            'menu_item' => $menuItem,
            'all_items' => $allItems,
            'tenant_name' => $this->getTenantName(),
        ]);
    }

    /**
     * Formulario para editar item del menú existente
     */
    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MenuItem $menuItem): Response
    {
        if ($request->isMethod('POST')) {
            $this->processForm($menuItem, $request);
            
            $menuItem->setUpdatedAt(new \DateTime());
            $this->entityManager->flush();
            
            // Invalidar caché
            $this->menuDefinition->invalidateCache($this->getTenantId($request));
            
            $this->addFlash('success', 'Item del menú actualizado exitosamente');
            return $this->redirectToRoute('admin_menu_config_index');
        }

        $allItems = $this->menuRepository->getAllForAdmin();
        
        return $this->render('admin/menu_config/edit.html.twig', [
            'menu_item' => $menuItem,
            'all_items' => $allItems,
            'tenant_name' => $this->getTenantName(),
        ]);
    }

    /**
     * Eliminar item del menú
     */
    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, MenuItem $menuItem): Response
    {
        $position = $menuItem->getPosition();
        $parent = $menuItem->getParent();
        
        $this->entityManager->remove($menuItem);
        $this->entityManager->flush();
        
        // Reordenar items restantes
        $this->menuRepository->reorderAfterDelete($position, $parent);
        
        // Invalidar caché
        $this->menuDefinition->invalidateCache($this->getTenantId($request));
        
        $this->addFlash('success', 'Item del menú eliminado exitosamente');
        return $this->redirectToRoute('admin_menu_config_index');
    }

    /**
     * Toggle habilitado/deshabilitado
     */
    #[Route('/{id}/toggle', name: 'toggle', methods: ['POST'])]
    public function toggle(Request $request, MenuItem $menuItem): Response
    {
        $menuItem->setEnabled(!$menuItem->isEnabled());
        $menuItem->setUpdatedAt(new \DateTime());
        
        $this->entityManager->flush();
        
        // Invalidar caché
        $this->menuDefinition->invalidateCache($this->getTenantId($request));
        
        $this->addFlash('success', 'Estado del item actualizado');
        return $this->redirectToRoute('admin_menu_config_index');
    }

    /**
     * Invalida manualmente el caché del menú
     */
    #[Route('/clear-cache', name: 'clear_cache', methods: ['POST'])]
    public function clearCache(Request $request): Response
    {
        $this->menuDefinition->invalidateCache($this->getTenantId($request));
        
        $this->addFlash('success', 'Caché del menú invalidado exitosamente');
        return $this->redirectToRoute('admin_menu_config_index');
    }

    /**
     * Procesa el formulario y actualiza el MenuItem
     */
    private function processForm(MenuItem $menuItem, Request $request): void
    {
        $menuItem->setName($request->request->get('name'));
        $menuItem->setLabel($request->request->get('label'));
        $menuItem->setRoute($request->request->get('route') ?: null);
        $menuItem->setIcon($request->request->get('icon') ?: null);
        $menuItem->setModule($request->request->get('module') ?: null);
        $menuItem->setPosition((int) $request->request->get('position', 0));
        $menuItem->setEnabled($request->request->has('enabled'));
        $menuItem->setVisibleInSidebar($request->request->has('visible_in_sidebar'));
        $menuItem->setRequiresAuth($request->request->has('requires_auth'));
        
        // Parent
        $parentId = $request->request->get('parent_id');
        if ($parentId) {
            $parent = $this->menuRepository->find($parentId);
            $menuItem->setParent($parent);
        } else {
            $menuItem->setParent(null);
        }
        
        // Required roles (JSON)
        $rolesInput = $request->request->get('required_roles');
        if ($rolesInput) {
            $roles = array_filter(array_map('trim', explode(',', $rolesInput)));
            $menuItem->setRequiredRoles($roles);
        } else {
            $menuItem->setRequiredRoles(null);
        }
    }

    /**
     * Obtiene el tenant ID desde la sesión
     */
    private function getTenantId(Request $request): string
    {
        return (string) ($request->getSession()->get('tenant_id') ?? 'default');
    }
}
