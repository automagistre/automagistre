<?php

declare(strict_types=1);

namespace App\MC\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\MC\Entity\McEquipment;
use App\MC\Entity\McLine;
use function assert;
use LogicException;
use Ramsey\Uuid\Uuid;
use stdClass;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class LineController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): stdClass
    {
        $dto = new stdClass();
        $equipment = $this->getEntity(McEquipment::class);
        if (!$equipment instanceof McEquipment) {
            throw new LogicException('Equipment required.');
        }

        $dto->id = null;
        $dto->equipment = $equipment;
        $dto->work = null;
        $dto->period = null;
        $dto->recommended = null;
        $dto->position = null;

        return $dto;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): McLine
    {
        $dto = $entity;
        assert($dto instanceof stdClass);

        $entity = new McLine(
            Uuid::uuid6(),
            $dto->equipment,
            $dto->work,
            $dto->period,
            $dto->recommended,
            $dto->position,
        );

        parent::persistEntity($entity);

        return $entity;
    }
}
