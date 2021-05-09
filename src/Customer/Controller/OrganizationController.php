<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Customer\Entity\OperandId;
use App\Customer\Entity\Organization;
use App\Customer\Form\OrganizationDto;
use App\Customer\Form\OrganizationType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class OrganizationController extends OperandController
{
    public function widgetAction(): Response
    {
        $request = $this->request;
        $em = $this->em;

        $dto = new OrganizationDto();

        $form = $this->createForm(OrganizationType::class, $dto)
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $id = OperandId::generate();

            $organization = new Organization(
                $id,
            );
            $organization->setName($dto->name);
            $organization->setEmail($dto->email);
            $organization->setTelephone($dto->telephone);

            $em->persist($organization);
            $em->flush();

            return new JsonResponse([
                'id' => $id->toString(),
                'text' => $this->display($id),
            ]);
        }

        return $this->render('easy_admin/widget.html.twig', [
            'id' => 'organization',
            'label' => 'Новый клиент (ЮЛ)',
            'form' => $form->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): Organization
    {
        return new Organization(OperandId::generate());
    }
}
