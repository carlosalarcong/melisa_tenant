<?php

namespace App\Controller\Admission;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Tenant\IdentificationType;
use App\Entity\Tenant\Person;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admission', name: 'app_admission_')]
class AdmissionController extends AbstractTenantAwareController
{
    public function __construct(
        private TenantEntityManager $entityManager
    ) {}

    #[Route('', name: 'index', methods: ['GET'])]
    #[Route('/hospitalization', name: 'hospitalization_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('admission/index.html.twig', $this->buildSearchViewData(
            request: $request,
            pageTitle: 'Admisión Hospitalaria',
            searchActionRoute: 'app_admission_hospitalization_index'
        ));
    }

    #[Route('/pre', name: 'pre_index', methods: ['GET'])]
    public function preIndex(Request $request): Response
    {
        return $this->render('admission/index.html.twig', $this->buildSearchViewData(
            request: $request,
            pageTitle: 'Pre-Admisión',
            searchActionRoute: 'app_admission_pre_index'
        ));
    }

    private function buildSearchViewData(Request $request, string $pageTitle, string $searchActionRoute): array
    {
        $searchTerm = trim((string) $request->query->get('q', ''));
        $selectedTypeId = $request->query->getInt('identification_type', 0);
        $patients = [];
        $searched = $searchTerm !== '';

        $identificationTypes = $this->entityManager->createQueryBuilder()
            ->select('it')
            ->from(IdentificationType::class, 'it')
            ->where('it.isActive = true')
            ->orderBy('it.name', 'ASC')
            ->getQuery()
            ->getResult();

        if ($searched) {
            $qb = $this->entityManager->createQueryBuilder()
                ->select('p', 'it')
                ->from(Person::class, 'p')
                ->leftJoin('p.identificationType', 'it')
                ->andWhere(
                    'LOWER(p.identification) LIKE :term OR
                     LOWER(p.name) LIKE :term OR
                     LOWER(p.lastName) LIKE :term OR
                     LOWER(CONCAT(p.name, \' \', p.lastName, \' \', COALESCE(p.middleName, \'\'))) LIKE :term'
                )
                ->setParameter('term', '%' . mb_strtolower($searchTerm) . '%')
                ->orderBy('p.id', 'DESC')
                ->setMaxResults(20);

            if ($selectedTypeId > 0) {
                $qb->andWhere('it.id = :typeId')
                    ->setParameter('typeId', $selectedTypeId);
            }

            $patients = $qb->getQuery()->getResult();
        }

        return [
            'page_title' => $pageTitle,
            'search_action_route' => $searchActionRoute,
            'identification_types' => $identificationTypes,
            'search_term' => $searchTerm,
            'selected_type_id' => $selectedTypeId,
            'patients' => $patients,
            'searched' => $searched,
        ];
    }
}
