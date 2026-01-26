<?php

namespace App\Controller\Maintainers\Commercial;

use App\Controller\AbstractMantenedorController;
use App\Entity\Tenant\Room;
use App\Form\Maintainers\Commercial\RoomType;
use App\Repository\Tenant\RoomRepository;
use App\Service\Export\ExportService;
use Doctrine\ORM\QueryBuilder;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maintainers/commercial/room')]
class RoomController extends AbstractMantenedorController
{
    public function __construct(
        private RoomRepository $roomRepository,
        TenantEntityManager $tenantEntityManager,
        ExportService $exportService
    ) {
        parent::__construct($tenantEntityManager);
        $this->setExportService($exportService);
    }

    protected function getData(Request $request): array|QueryBuilder
    {
        return $this->roomRepository->createQueryBuilder('r')
            ->leftJoin('r.clinic', 'c')
            ->addSelect('c')
            ->orderBy('r.id', 'DESC');
    }

    protected function getColumns(): array
    {
        return [
            'id' => 'ID',
            'roomNumber' => 'Número',
            'name' => 'Nombre',
            'roomType' => 'Tipo',
            'floor' => 'Piso',
            'capacity' => 'Capacidad',
            'status' => 'Estado',
            'isActive' => 'Activo'
        ];
    }

    protected function getTemplatePath(): string
    {
        return 'maintainers/commercial/room/index.html.twig';
    }

    protected function getFormType(): string
    {
        return RoomType::class;
    }

    protected function createNewEntity(): object
    {
        return new Room();
    }

    protected function getIndexRoute(): string
    {
        return 'app_maintainers_commercial_room_index';
    }

    protected function getPageTitle(string $action = 'index'): string
    {
        return match($action) {
            'create' => 'Crear Sala',
            'edit' => 'Editar Sala',
            default => 'Salas'
        };
    }

    #[Route('', name: 'app_maintainers_commercial_room_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->handleIndex($request);
    }

    #[Route('/create', name: 'app_maintainers_commercial_room_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        return $this->handleCreate($request);
    }

    #[Route('/{id}/edit', name: 'app_maintainers_commercial_room_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Room $room): Response
    {
        return $this->handleEdit($request, $room->getId());
    }

    #[Route('/{id}', name: 'app_maintainers_commercial_room_delete', methods: ['DELETE'])]
    public function delete(Request $request, Room $room): Response
    {
        return $this->handleDelete($request, $room->getId());
    }

    #[Route('/export', name: 'app_maintainers_commercial_room_export', methods: ['GET'])]
    public function export(Request $request): Response
    {
        return $this->handleExport($request);
    }
}
