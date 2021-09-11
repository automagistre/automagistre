<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Customer\Entity\OperandId;
use App\Customer\Entity\Organization;
use App\Customer\Form\OrganizationDto;
use App\Customer\Form\OrganizationType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function in_array;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class OrganizationController extends OperandController
{
    protected function initialize(Request $request): void
    {
        parent::initialize($request);

        if (in_array($request->query->get('action'), ['edit', 'delete'], true)) {
            $easyadmin = $request->attributes->get('easyadmin');
            $easyadmin['item'] = $this->registry->get(Organization::class, $request->query->get('id'));

            $request->attributes->set('easyadmin', $easyadmin);
        }
    }

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
            $organization->name = $dto->name;
            $organization->email = $dto->email;
            $organization->telephone = $dto->telephone;

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
