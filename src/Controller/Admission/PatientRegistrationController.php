<?php

namespace App\Controller\Admission;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Tenant\Country;
use App\Entity\Tenant\EducationLevel;
use App\Entity\Tenant\Gender;
use App\Entity\Tenant\IdentificationType;
use App\Entity\Tenant\MaritalStatus;
use App\Entity\Tenant\Occupation;
use App\Entity\Tenant\Person;
use App\Entity\Tenant\Religion;
use Hakam\MultiTenancyBundle\Doctrine\ORM\TenantEntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admission/patient', name: 'app_admission_patient_')]
class PatientRegistrationController extends AbstractTenantAwareController
{
    public function __construct(
        private TenantEntityManager $entityManager
    ) {}

    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        $identificationTypes = $this->entityManager->getRepository(IdentificationType::class)->findBy([], ['name' => 'ASC']);
        $genders = $this->entityManager->getRepository(Gender::class)->findBy([], ['name' => 'ASC']);
        $countries = $this->entityManager->getRepository(Country::class)->findBy([], ['name' => 'ASC']);
        $maritalStatuses = $this->entityManager->getRepository(MaritalStatus::class)->findBy([], ['name' => 'ASC']);
        $occupations = $this->entityManager->getRepository(Occupation::class)->findBy([], ['name' => 'ASC']);
        $religions = $this->entityManager->getRepository(Religion::class)->findBy([], ['name' => 'ASC']);
        $educationLevels = $this->entityManager->getRepository(EducationLevel::class)->findBy([], ['name' => 'ASC']);

        $formData = [
            'identification_type' => $request->query->getInt('identification_type', 0),
            'identification' => (string) $request->query->get('identification', ''),
            'admission_type' => (string) $request->query->get('admission_type', 'hospitalaria'),
            'birth_date' => '',
            'name' => '',
            'last_name' => '',
            'middle_name' => '',
            'mobile_phone' => '',
            'home_phone' => '',
            'work_phone' => '',
            'email' => '',
            'secondary_email' => '',
            'social_name' => '',
            'number_of_children' => '',
            'gender' => 0,
            'country' => 0,
            'marital_status' => 0,
            'occupation' => 0,
            'religion' => 0,
            'education_level' => 0,
        ];

        if ($request->isMethod('POST')) {
            $formData = [
                'identification_type' => $request->request->getInt('identification_type', 0),
                'identification' => trim((string) $request->request->get('identification', '')),
                'admission_type' => (string) $request->request->get('admission_type', 'hospitalaria'),
                'birth_date' => trim((string) $request->request->get('birth_date', '')),
                'name' => trim((string) $request->request->get('name', '')),
                'last_name' => trim((string) $request->request->get('last_name', '')),
                'middle_name' => trim((string) $request->request->get('middle_name', '')),
                'mobile_phone' => trim((string) $request->request->get('mobile_phone', '')),
                'home_phone' => trim((string) $request->request->get('home_phone', '')),
                'work_phone' => trim((string) $request->request->get('work_phone', '')),
                'email' => trim((string) $request->request->get('email', '')),
                'secondary_email' => trim((string) $request->request->get('secondary_email', '')),
                'social_name' => trim((string) $request->request->get('social_name', '')),
                'number_of_children' => trim((string) $request->request->get('number_of_children', '')),
                'gender' => $request->request->getInt('gender', 0),
                'country' => $request->request->getInt('country', 0),
                'marital_status' => $request->request->getInt('marital_status', 0),
                'occupation' => $request->request->getInt('occupation', 0),
                'religion' => $request->request->getInt('religion', 0),
                'education_level' => $request->request->getInt('education_level', 0),
            ];

            if ($formData['identification'] === '' || $formData['name'] === '' || $formData['last_name'] === '' || $formData['mobile_phone'] === '' || $formData['birth_date'] === '') {
                $this->addFlash('danger', 'Completa los campos obligatorios del paciente.');
            } else {
                $exists = $this->entityManager->createQueryBuilder()
                    ->select('p.id')
                    ->from(Person::class, 'p')
                    ->leftJoin('p.identificationType', 'it')
                    ->where('p.identification = :identification')
                    ->setParameter('identification', $formData['identification']);

                if ($formData['identification_type'] > 0) {
                    $exists->andWhere('it.id = :typeId')->setParameter('typeId', $formData['identification_type']);
                }

                if (!empty($exists->getQuery()->getScalarResult())) {
                    $this->addFlash('warning', 'Ya existe un paciente con esa identificación.');
                }
            }

            if (count($request->getSession()->getFlashBag()->peek('danger')) === 0 && count($request->getSession()->getFlashBag()->peek('warning')) === 0) {
                try {
                    $birthDateAt = new \DateTimeImmutable($formData['birth_date']);
                } catch (\Exception) {
                    $this->addFlash('danger', 'La fecha de nacimiento no es válida.');
                    $birthDateAt = null;
                }
            }

            if (count($request->getSession()->getFlashBag()->peek('danger')) === 0 && count($request->getSession()->getFlashBag()->peek('warning')) === 0) {
                $person = new Person();
                $person->setIdentification($formData['identification']);
                $person->setName($formData['name']);
                $person->setLastName($formData['last_name']);
                $person->setMiddleName($formData['middle_name'] === '' ? null : $formData['middle_name']);
                $person->setMobilePhone($formData['mobile_phone']);
                $person->setHomePhone($formData['home_phone'] === '' ? null : $formData['home_phone']);
                $person->setWorkPhone($formData['work_phone'] === '' ? null : $formData['work_phone']);
                $person->setEmail($formData['email'] === '' ? null : $formData['email']);
                $person->setSecondaryEmail($formData['secondary_email'] === '' ? null : $formData['secondary_email']);
                $person->setSocialName($formData['social_name'] === '' ? null : $formData['social_name']);
                $person->setNumberOfChildren($formData['number_of_children'] === '' ? null : (int) $formData['number_of_children']);
                $person->setCreatedAt(new \DateTimeImmutable());
                $person->setUpdatedAt(new \DateTimeImmutable());
                $person->setBirthDateAt($birthDateAt);

                $this->assignRelation($person, IdentificationType::class, 'setIdentificationType', $formData['identification_type']);
                $this->assignRelation($person, Gender::class, 'setGender', $formData['gender']);
                $this->assignRelation($person, Country::class, 'setNacionality', $formData['country']);
                $this->assignRelation($person, MaritalStatus::class, 'setMaritalStatus', $formData['marital_status']);
                $this->assignRelation($person, Occupation::class, 'setOccupation', $formData['occupation']);
                $this->assignRelation($person, Religion::class, 'setReligion', $formData['religion']);
                $this->assignRelation($person, EducationLevel::class, 'setEducationLevel', $formData['education_level']);

                $this->entityManager->persist($person);
                $this->entityManager->flush();

                $this->addFlash('success', 'Persona creada. Continúa con la admisión.');

                return $this->redirectToRoute('app_admission_wizard_step1', [
                    'patientId' => $person->getId(),
                    'type' => $formData['admission_type'] === 'pre' ? 'pre' : 'hospitalaria',
                ]);
            }
        }

        return $this->render('admission/patient/register.html.twig', [
            'page_title' => 'Registro de paciente',
            'identification_types' => $identificationTypes,
            'genders' => $genders,
            'countries' => $countries,
            'marital_statuses' => $maritalStatuses,
            'occupations' => $occupations,
            'religions' => $religions,
            'education_levels' => $educationLevels,
            'form_data' => $formData,
        ]);
    }

    private function assignRelation(Person $person, string $className, string $setter, int $id): void
    {
        if ($id <= 0) {
            return;
        }

        $entity = $this->entityManager->find($className, $id);
        if ($entity !== null) {
            $person->{$setter}($entity);
        }
    }
}
