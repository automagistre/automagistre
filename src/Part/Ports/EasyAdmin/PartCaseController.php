<?php

declare(strict_types=1);

namespace App\Part\Ports\EasyAdmin;

use App\Controller\EasyAdmin\AbstractController;
use App\Part\Domain\Part;
use App\Part\Domain\PartCase;
use function assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartCaseController extends AbstractController
{
    protected function createNewEntity(): PartCaseDTO
    {
        /** @var Part|null $part */
        $part = $this->getEntity(Part::class);

        return new PartCaseDTO($part);
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): PartCase
    {
        $model = $entity;
        assert($model instanceof PartCaseDTO);

        $entity = new PartCase($model->part->toId(), $model->vehicle->toId());

        parent::persistEntity($entity);

        return $entity;
    }
}
