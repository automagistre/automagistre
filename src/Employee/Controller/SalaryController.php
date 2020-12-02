<?php

declare(strict_types=1);

namespace App\Employee\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\EasyAdmin\Form\AutocompleteType;
use App\Employee\Entity\Employee;
use App\Employee\Entity\EmployeeId;
use App\Employee\Entity\Salary;
use App\Employee\Entity\SalaryId;
use App\Employee\Form\PayDayType;
use App\Employee\Form\SalaryDto;
use App\Form\Type\MoneyType;
use LogicException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class SalaryController extends AbstractController
{
    protected function newAction(): Response
    {
        $employeeId = $this->getIdentifier(EmployeeId::class);
        if (!$employeeId instanceof EmployeeId) {
            throw new LogicException('Employee required.');
        }

        $dto = new SalaryDto();
        $dto->employeeId = $employeeId;

        $form = $this->createFormBuilder($dto)
            ->add('employeeId', AutocompleteType::class, [
                'label' => 'Работник',
                'class' => Employee::class,
            ])
            ->add('payday', PayDayType::class, [
                'label' => 'День начисления',
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'Сумма',
            ])
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            $em->persist(
                new Salary(
                    SalaryId::generate(),
                    $dto->employeeId,
                    $dto->payday,
                    $dto->amount,
                )
            );
            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/simple.html.twig', [
            'content_title' => 'Создать ежемесячный оклад для '.$this->display($employeeId),
            'form' => $form->createView(),
        ]);
    }
}
