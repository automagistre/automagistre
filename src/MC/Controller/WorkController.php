<?php

declare(strict_types=1);

namespace App\MC\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\MC\Entity\McWork;
use App\MC\Entity\McWorkId;
use stdClass;
use function assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class WorkController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): stdClass
    {
        $dto = new stdClass();

        $dto->id = null;
        $dto->name = null;
        $dto->description = null;
        $dto->price = null;
        $dto->comment = null;

        return $dto;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): McWork
    {
        $dto = $entity;
        assert($dto instanceof stdClass);

        $entity = new McWork(
            McWorkId::generate(),
            $dto->name,
            $dto->description,
            $dto->price,
            $dto->comment,
        );

        parent::persistEntity($entity);

        return $entity;
    }
}
