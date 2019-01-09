<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Tenant\Employee;
use App\Events;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EmployeeController extends AbstractController
{
    public function fireAction(): Response
    {
        $entity = $this->findCurrentEntity();
        if (!$entity instanceof Employee) {
            throw new \LogicException('Employee required.');
        }

        if ($entity->isFired()) {
            $this->addFlash('error', \sprintf('Сотрудник "%s" уже уволен', $entity));

            return $this->redirectToReferrer();
        }

        $entity->fire();
        $this->em->flush();

        $this->event(Events::EMPLOYEE_FIRED, $entity);

        return $this->redirectToReferrer();
    }

    /**
     * @param Employee $entity
     */
    protected function persistEntity($entity): void
    {
        parent::persistEntity($entity);

        $this->event(Events::EMPLOYEE_CREATED, $entity);
    }
}
