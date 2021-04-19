<?php

declare(strict_types=1);

namespace App\Note\View;

use App\Note\Entity\Notes;
use App\Note\Entity\NoteView;
use App\Shared\Doctrine\Registry;
use Ramsey\Uuid\UuidInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class NoteExtension extends AbstractExtension
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'notes_by_subject',
                fn (UuidInterface $id) => $this->registry->repository(NoteView::class)
                    ->findBy(['subject' => $id], ['id' => 'ASC'])
            ),
            new TwigFunction('notes_implement', fn (object $object) => $object instanceof Notes),
        ];
    }
}
