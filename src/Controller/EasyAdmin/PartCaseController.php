<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Landlord\Part;
use App\Entity\Landlord\PartCase;
use LogicException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartCaseController extends AbstractController
{
    protected function createNewEntity(): PartCase
    {
        $part = $this->getEntity(Part::class);
        if (!$part instanceof Part) {
            throw new LogicException('Part required.');
        }

        return new PartCase($part);
    }
}
