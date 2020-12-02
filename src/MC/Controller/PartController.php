<?php

declare(strict_types=1);

namespace App\MC\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\MC\Entity\McLine;
use App\MC\Entity\McPart;
use App\MC\Form\McPartDto;
use function assert;
use LogicException;
use Ramsey\Uuid\Uuid;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class PartController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): McPartDto
    {
        $dto = new McPartDto();

        $line = $this->getEntity(McLine::class);
        if (!$line instanceof McLine) {
            throw new LogicException('Line required.');
        }

        $dto->line = $line;

        return $dto;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): McPart
    {
        $dto = $entity;
        assert($dto instanceof McPartDto);

        $entity = new McPart(
            Uuid::uuid6(),
            $dto->line,
            $dto->partId,
            $dto->quantity,
            $dto->recommended
        );

        parent::persistEntity($entity);

        return $entity;
    }
}
