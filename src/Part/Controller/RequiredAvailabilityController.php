<?php

declare(strict_types=1);

namespace App\Part\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use App\Part\Entity\RequiredAvailability;
use App\Part\Form\RequiredAvailabilityDto;
use App\Part\Form\RequiredAvailabilityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use function array_map;
use function explode;
use function is_string;
use function trim;
use const PHP_EOL;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class RequiredAvailabilityController extends AbstractController
{
    public function newAction(): Response
    {
        $request = $this->request;

        $partId = $this->getIdentifierOrNull(PartId::class);

        if (!$partId instanceof PartId) {
            throw new BadRequestException('PartId required.');
        }
        $partView = $this->registry->get(PartView::class, $partId);

        $dto = new RequiredAvailabilityDto();
        $dto->partId = $partId;
        $dto->orderFromQuantity = $partView->orderFromQuantity;
        $dto->orderUpToQuantity = $partView->orderUpToQuantity;

        $form = $this->createForm(RequiredAvailabilityType::class, $dto)
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;
            $em->persist(new RequiredAvailability($dto->partId, $dto->orderFromQuantity, $dto->orderUpToQuantity));
            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => 'Новое значение Запасов',
            'form' => $form->createView(),
        ]);
    }

    public function importAction(): Response
    {
        $request = $this->request;

        $form = $this->createFormBuilder()
            ->add('text', TextareaType::class, [
                'label' => false,
                'required' => true,
            ])
            ->add('parts', CollectionType::class, [
                'entry_type' => RequiredAvailabilityType::class,
                'entry_options' => [
                    'disabled_part' => false,
                ],
                'label' => false,
                'allow_add' => true,
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, static function (FormEvent $event): void {
                $data = $event->getData();

                $text = $data['text'] ?? '';

                if ('' === $text || !is_string($text)) {
                    return;
                }

                /** @var array<int, string> $lines */
                $lines = explode(PHP_EOL, trim($text));
                $data['parts'] = array_map(static function (string $line): array {
                    $pieces = explode(',', trim($line));

                    return [
                        'partId' => [
                            'autocomplete' => $pieces[0] ?? '',
                        ],
                        'orderUpToQuantity' => $pieces[1] ?? '',
                        'orderFromQuantity' => $pieces[2] ?? '',
                    ];
                }, $lines);

                $event->setData($data);
            })
            ->getForm()
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            foreach ($form->getData()['parts'] ?? [] as $dto) {
                /** @var RequiredAvailabilityDto $dto */
                $em->persist(new RequiredAvailability($dto->partId, $dto->orderFromQuantity, $dto->orderUpToQuantity));
            }

            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => 'Импорт запасов',
            'button' => 'Импортировать',
            'form' => $form->createView(),
        ]);
    }
}
