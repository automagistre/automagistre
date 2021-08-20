<?php

declare(strict_types=1);

namespace App\Note\View;

use App\Doctrine\Registry;
use App\Note\Entity\NoteView;
use Ramsey\Uuid\UuidInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class NoteExtension extends AbstractExtension
{
    public function __construct(private Registry $registry)
    {
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
                    ->findBy(['subject' => $id], ['id' => 'ASC']),
            ),
        ];
    }
}
