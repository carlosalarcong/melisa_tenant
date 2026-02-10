<?php

namespace App\Controller\Admission;

use App\Controller\AbstractTenantAwareController;
use App\Entity\Tenant\IdentificationType;
use App\Entity\Tenant\Person;
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
        $identificationTypes = $this->entityManager->createQueryBuilder()
            ->select('it')
            ->from(IdentificationType::class, 'it')
            ->where('it.isActive = true')
            ->orderBy('it.name', 'ASC')
            ->getQuery()
            ->getResult();

        $prefillTypeId = $request->query->getInt('identification_type', 0);
        $prefillIdentification = (string) $request->query->get('identification', '');

        if ($request->isMethod('POST')) {
            $typeId = $request->request->getInt('identification_type', 0);
            $identification = trim((string) $request->request->get('identification', ''));
            $name = trim((string) $request->request->get('name', ''));
            $lastName = trim((string) $request->request->get('last_name', ''));
            $middleName = trim((string) $request->request->get('middle_name', ''));
            $mobilePhone = trim((string) $request->request->get('mobile_phone', ''));
            $email = trim((string) $request->request->get('email', ''));
            $birthDate = trim((string) $request->request->get('birth_date', ''));

            if ($identification === '' || $name === '' || $lastName === '' || $mobilePhone === '' || $birthDate === '') {
                $this->addFlash('danger', 'Completa los campos obligatorios del paciente.');
            } else {
                try {
                    $birthDateAt = new \DateTimeImmutable($birthDate);
                } catch (\Exception) {
                    $this->addFlash('danger', 'La fecha de nacimiento no es válida.');

                    return $this->render('admission/patient/register.html.twig', [
                        'page_title' => 'Registro de paciente',
                        'identification_types' => $identificationTypes,
                        'prefill_type_id' => $typeId,
                        'prefill_identification' => $identification,
                    ]);
                }

                $person = new Person();
                $person->setIdentification($identification);
                $person->setName($name);
                $person->setLastName($lastName);
                $person->setMiddleName($middleName === '' ? null : $middleName);
                $person->setMobilePhone($mobilePhone);
                $person->setEmail($email === '' ? null : $email);
                $person->setCreatedAt(new \DateTimeImmutable());
                $person->setUpdatedAt(new \DateTimeImmutable());
                $person->setBirthDateAt($birthDateAt);

                if ($typeId > 0) {
                    $type = $this->entityManager->find(IdentificationType::class, $typeId);
                    if ($type instanceof IdentificationType) {
                        $person->setIdentificationType($type);
                    }
                }

                $this->entityManager->persist($person);
                $this->entityManager->flush();

                $this->addFlash('success', 'Persona creada. Continúa con la admisión.');

                return $this->redirectToRoute('app_admission_wizard_step1', [
                    'patientId' => $person->getId(),
                ]);
            }

            $prefillTypeId = $typeId;
            $prefillIdentification = $identification;
        }

        return $this->render('admission/patient/register.html.twig', [
            'page_title' => 'Registro de paciente',
            'identification_types' => $identificationTypes,
            'prefill_type_id' => $prefillTypeId,
            'prefill_identification' => $prefillIdentification,
        ]);
    }
}
