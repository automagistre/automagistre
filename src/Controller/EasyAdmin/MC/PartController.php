<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin\MC;

use App\Controller\EasyAdmin\AbstractController;
use App\Entity\Landlord\MC\Line;
use App\Entity\Landlord\MC\Part;
use LogicException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): Part
    {
        $entity = new Part();

        $line = $this->getEntity(Line::class);
        if (!$line instanceof Line) {
            throw new LogicException('Line required.');
        }

        $entity->line = $line;

        return $entity;
    }
}
