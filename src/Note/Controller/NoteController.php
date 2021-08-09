<?php

declare(strict_types=1);

namespace App\Note\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Note\Entity\Note;
use App\Note\Form\NoteDeleteDto;
use App\Note\Form\NoteDto;
use App\Note\Form\NoteTypeType;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use function is_string;
use function sprintf;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class NoteController extends AbstractController
{
    protected function newAction(): Response
    {
        $subject = $this->request->query->get('subject');

        if (!is_string($subject) || !Uuid::isValid($subject)) {
            throw new BadRequestHttpException(sprintf('Wrong subject id "%s"', (string) $subject));
        }

        $dto = new NoteDto();

        $form = $this->createFormBuilder($dto)
            ->add('type', NoteTypeType::class, [
                'label' => 'Тип',
            ])
            ->add('text', TextType::class, [
                'label' => 'Текст',
            ])
            ->getForm()
            ->handleRequest($this->request)
        ;

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

    public function removeAction(): Response
    {
        $note = $this->findCurrentEntity();

        if (!$note instanceof Note) {
            throw new BadRequestHttpException();
        }

        $dto = new NoteDeleteDto();

        $form = $this->createFormBuilder($dto)
            ->add('description', TextType::class, [
                'label' => 'Комментарий',
                'required' => false,
            ])
            ->getForm()
            ->handleRequest($this->request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $note->delete($dto->description);

            $this->em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => sprintf('Подтвердите удаление заметки "<strong>%s</strong>"', $note->text),
            'form' => $form->createView(),
        ]);
    }
}
