<?php

declare(strict_types=1);

namespace App\Income\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Income\Entity\Income;
use App\Income\Entity\IncomeId;
use App\Income\Entity\IncomePart;
use App\Income\Entity\IncomePartId;
use App\Income\Form\IncomePartDto;
use App\Shared\Identifier\IdentifierFormatter;
use LogicException;
use function assert;
use function in_array;
use function sprintf;
use function urlencode;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class IncomePartController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function isActionAllowed($actionName): bool
    {
        if (in_array($actionName, ['edit', 'delete'], true)) {
            $incomePart = $this->findCurrentEntity();

            if (!$incomePart instanceof IncomePart) {
                throw new LogicException('IncomePart required.');
            }

            if (!$incomePart->income->isEditable()) {
                return false;
            }
        }

        return parent::isActionAllowed($actionName);
    }

    protected function createNewEntity(): IncomePartDto
    {
        return new IncomePartDto(
            $this->getIdentifier(IncomeId::class),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        $dto = $entity;
        assert($dto instanceof IncomePartDto);

        $income = $this->registry->get(Income::class, $dto->incomeId);

        $entity = new IncomePart(
            IncomePartId::generate(),
            $income,
            $dto->partId,
            $dto->price,
            $dto->quantity,
        );

        parent::persistEntity($entity);

        $this->setReferer($this->generateEasyPath('IncomePart', 'new', [
            'id' => $entity->toId()->toString(),
            'income_id' => $income->toId()->toString(),
            'referer' => urlencode($this->generateEasyPath('Income', 'show', ['id' => $income->toId()->toString()])),
        ]));

        $this->addFlash('success', sprintf(
            'Запчасть "%s" в количестве "%s" добавлена в приход.',
            $this->container->get(IdentifierFormatter::class)->format($entity->partId),
            $entity->quantity / 100,
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function updateEntity($entity): void
    {
        assert($entity instanceof IncomePart);

        parent::updateEntity($entity);

        $income = $entity->income;

        $this->setReferer(
            $this->generateEasyPath('Income', 'show', [
                'id' => $income->toId()->toString(),
            ]),
        );
    }
}
