<?php

declare(strict_types=1);

namespace App\Part\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\EasyAdmin\Form\AutocompleteType;
use App\Part\Entity\Part;
use App\Part\Entity\PartId;
use App\Part\Entity\PartView;
use App\Part\Manager\PartManager;
use LogicException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class PartCrossController extends AbstractController
{
    private PartManager $partManager;

    public function __construct(PartManager $partManager)
    {
        $this->partManager = $partManager;
    }

    public function crossAction(): Response
    {
        $leftId = $this->getIdentifierOrNull(PartId::class);

        if (!$leftId instanceof PartId) {
            throw new LogicException('Part required.');
        }

        $form = $this->createFormBuilder()
            ->add('right', AutocompleteType::class, [
                'class' => Part::class,
                'label' => 'Аналог',
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\NotEqualTo(['value' => $leftId]),
                ],
            ])
            ->getForm()
            ->handleRequest($this->request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $this->partManager->cross($leftId, $form->get('right')->getData());

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/part/cross.html.twig', [
            'part' => $this->registry->get(PartView::class, $leftId),
            'form' => $form->createView(),
        ]);
    }

    public function uncrossAction(): Response
    {
        $partId = $this->getIdentifierOrNull(PartId::class);

        if (!$partId instanceof PartId) {
            throw new LogicException('Part required.');
        }

        $this->partManager->uncross($partId);

        return $this->redirectToReferrer();
    }
}
