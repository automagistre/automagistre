<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Part;
use App\Entity\Supply;
use App\Form\Model;
use LogicException;
use Symfony\Component\Form\FormInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SupplyController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function isActionAllowed($actionName): bool
    {
        if (\in_array($actionName, ['edit', 'delete'], true)) {
            $supply = $this->findCurrentEntity();
            if (!$supply instanceof Supply) {
                throw new LogicException('Supply required.');
            }

            if (null !== $supply->getReceivedAt()) {
                return false;
            }
        }

        return parent::isActionAllowed($actionName);
    }

    protected function createNewEntity(): Model\Supply
    {
        $model = new Model\Supply();
        $model->quantity = $this->request->query->getInt('quantity');

        $part = $this->getEntity(Part::class);
        if ($part instanceof Part) {
            $model->part = $part;
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntityForm($entity, array $entityProperties, $view): FormInterface
    {
        if ($entity instanceof Supply) {
            $entity = Model\Supply::createFromSupply($entity);

            $this->request->attributes->set('model', $entity);
        }

        return parent::createEntityForm($entity, $entityProperties, $view);
    }

    /**
     * @param Model\Supply $model
     */
    protected function persistEntity($model): void
    {
        $supplier = $model->supplier;
        $part = $model->part;

        $supply = $this->em->getRepository(Supply::class)->findOneBy([
            'supplier' => $supplier,
            'part' => $part,
            'receivedAt' => null,
        ]);

        if ($supply instanceof Supply) {
            $this->addFlash('error', 'Такая поставка уже существует, отредактируйте её (Вы сейчас на ней)');

            $this->setReferer($this->generateEasyPath($supply, 'edit', [
                'referer' => \urlencode($this->generateEasyPath($supply, 'list')),
            ]));

            return;
        }

        parent::persistEntity(
            new Supply($supplier, $part, $model->price, $model->quantity)
        );
    }

    /**
     * @param Supply $entity
     */
    protected function updateEntity($entity): void
    {
        $model = $this->request->attributes->get('model');
        if (!$model instanceof Model\Supply) {
            throw new LogicException('Model\Supply required.');
        }

        $entity->updateFromModel($model);

        parent::updateEntity($entity);
    }
}
