<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Part;
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

        $part = $this->getEntity(Part::class);
        if ($part instanceof Part) {
            $qb->andWhere('entity.part = :part')
                ->setParameter('part', $part);
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
