<?php

declare(strict_types=1);

namespace App\Income\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Income\Entity\Income;
use App\Income\Entity\IncomePart;
use App\Income\Entity\IncomePartId;
use App\Shared\Identifier\IdentifierFormatter;
use function assert;
use function in_array;
use LogicException;
use function sprintf;
use function urlencode;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
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

            if (!$incomePart->getIncome()->isEditable()) {
                return false;
            }
        }

        return parent::isActionAllowed($actionName);
    }

    protected function createNewEntity(): IncomePart
    {
        $entity = new IncomePart(IncomePartId::generate());

        $income = $this->getEntity(Income::class);
        if (!$income instanceof Income) {
            throw new LogicException('Income required.');
        }

        $entity->setIncome($income);

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        assert($entity instanceof IncomePart);

        parent::persistEntity($entity);

        $income = $entity->getIncome();
        assert($income instanceof Income);

        $this->setReferer($this->generateEasyPath('IncomePart', 'new', [
            'id' => $entity->toId()->toString(),
            'income_id' => $income->toId()->toString(),
            'referer' => urlencode($this->generateEasyPath('Income', 'show', ['id' => $income->toId()->toString()])),
        ]));

        $this->addFlash('success', sprintf(
            'Запчасть "%s" в количестве "%s" добавлена в приход.',
            $this->container->get(IdentifierFormatter::class)->format($entity->partId),
            $entity->getQuantity() / 100
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function updateEntity($entity): void
    {
        assert($entity instanceof IncomePart);

        parent::updateEntity($entity);

        $income = $entity->getIncome();
        assert($income instanceof Income);

        $this->setReferer($this->generateEasyPath('Income', 'show', ['id' => $income->toId()]));
    }
}
