<?php

declare(strict_types=1);

namespace App\Part\Ports\EasyAdmin;

use App\Controller\EasyAdmin\AbstractController;
use App\Part\Domain\Part;
use App\Part\Domain\PartCase;
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
