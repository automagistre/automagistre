<?php

declare(strict_types=1);

namespace App\Publish\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Publish\Entity\Publish;
use App\Publish\Form\PublishDto;
use App\Publish\Form\PublishType;
use App\Shared\Doctrine\Registry;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class PublishController extends AbstractController
{
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function indexAction(Request $request): Response
    {
        $this->request = $request; // for redirectToReferrer method

        $dto = new PublishDto();
        $form = $this->createForm(PublishType::class, $dto)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->registry->manager();

            $em->persist(
                Publish::create(
                    Uuid::fromString($dto->id),
                    $dto->publish,
                ),
            );
            $em->flush();

            return $this->redirectToReferrer();
        }

        throw new BadRequestHttpException();
    }
}
