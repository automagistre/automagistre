<?php

declare(strict_types=1);

namespace App\Appeal\Controller;

use App\Appeal\Entity\AppealId;
use App\Appeal\Entity\AppealView;
use App\Appeal\Entity\Status;
use App\Appeal\Enum\AppealStatus;
use App\EasyAdmin\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function assert;

final class AppealController extends AbstractController
{
    public function statusAction(Request $request): Response
    {
        $appealId = $this->getIdentifier(AppealId::class);

        $status = AppealStatus::create($request->query->getInt('status'));

        $em = $this->em;
        $em->persist(Status::create($appealId, $status));
        $em->flush();

        return $this->redirectToReferrer();
    }

    /**
     * {@inheritDoc}
     */
    protected function createListQueryBuilder(
        $entityClass,
        $sortDirection,
        $sortField = null,
        $dqlFilter = null,
    ): \Doctrine\ORM\QueryBuilder {
        $qb = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        $qb
            ->orderBy('entity.status', 'ASC')
            ->addOrderBy('entity.createdAt', 'DESC')
        ;

        return $qb;
    }

    /**
     * {@inheritDoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('show' === $actionName) {
            $entity = $parameters['entity'];
            assert($entity instanceof AppealView);

            /** @phpstan-ignore-next-line */
            $parameters['appeal'] = $this->registry->get($entity->type->toEntityClass(), $entity->toId());
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }
}
