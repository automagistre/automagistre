<?php

declare(strict_types=1);

namespace App\Note\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Note\Entity\Note;
use App\Note\Form\NoteDto;
use App\Note\Form\NoteTypeType;
use function is_string;
use Ramsey\Uuid\Uuid;
use function sprintf;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class NoteController extends AbstractController
{
    protected function newAction(): Response
    {
        $subject = $this->request->query->get('subject');
        if (!is_string($subject) || !Uuid::isValid($subject)) {
            throw new BadRequestHttpException(sprintf('Wrong subject id "%s"', (string) $subject));
        }

        $dto = $this->createWithoutConstructor(NoteDto::class);

        $form = $this->createFormBuilder($dto)
            ->add('type', NoteTypeType::class, [
                'label' => 'Тип',
            ])
            ->add('text', TextType::class, [
                'label' => 'Текст',
            ])
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            $em->persist(
                new Note(
                    Uuid::fromString($subject),
                    $dto->type,
                    $dto->text,
                ),
            );

            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => 'Создать заметку',
            'form' => $form->createView(),
        ]);
    }
}
