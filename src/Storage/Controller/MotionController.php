<?php

declare(strict_types=1);

namespace App\Storage\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Part\Domain\Part;
use App\Part\Domain\PartId;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MotionController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected function createListQueryBuilder(
        $entityClass,
        $sortDirection,
        $sortField = null,
        $dqlFilter = null
    ): QueryBuilder {
        $qb = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        $request = $this->request;

        $filter = $request->query->get('filter');

        if ('income' === $filter) {
            $qb->andWhere('entity.quantity >= 0');
        } elseif ('outcome' === $filter) {
            $qb->andWhere('entity.quantity <= 0');
        }

        $partId = $this->getIdentifier(PartId::class);
        if ($partId instanceof PartId) {
            $qb->andWhere('entity.partId = :part')
                ->setParameter('part', $partId);
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('list' === $actionName) {
            $parameters['part'] = $this->getEntity(Part::class);
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }
}
