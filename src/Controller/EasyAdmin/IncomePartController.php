<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Income;
use App\Entity\IncomePart;
use LogicException;

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
        if (\in_array($actionName, ['edit', 'delete'], true)) {
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
        $entity = new IncomePart();

        $income = $this->getEntity(Income::class);
        if (!$income instanceof Income) {
            throw new LogicException('Income required.');
        }

        $entity->setIncome($income);

        return $entity;
    }

    /**
     * @param IncomePart $entity
     */
    protected function persistEntity($entity): void
    {
        parent::persistEntity($entity);

        $this->setReferer($this->generateEasyPath($entity, 'new', [
            'income_id' => $entity->getIncome()->getId(),
            'referer' => \urlencode($this->generateEasyPath($entity->getIncome(), 'show')),
        ]));

        $this->addFlash('success', \sprintf(
            'Запчасть "%s" в количестве "%s" добавлена в приход.',
            $entity->getPart(),
            $entity->getQuantity() / 100
        ));
    }

    /**
     * @param IncomePart $entity
     */
    protected function updateEntity($entity): void
    {
        parent::updateEntity($entity);

        $this->setReferer($this->generateEasyPath($entity->getIncome(), 'show'));
    }
}
