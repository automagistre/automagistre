<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Car\Repository\CarCustomerRepository;
use App\Customer\Entity\CustomerTransactionView;
use App\EasyAdmin\Controller\AbstractController;
use App\Note\Entity\NoteView;
use App\Order\Entity\Order;
use App\Payment\Manager\PaymentManager;
use Symfony\Component\HttpFoundation\Response;
use function array_merge;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class OperandController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            PaymentManager::class,
            CarCustomerRepository::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('show' === $actionName) {
            $operand = $parameters['entity'];
            /** @var CarCustomerRepository $carRepository */
            $carRepository = $this->container->get(CarCustomerRepository::class);

            $parameters['cars'] = $carRepository->carsByCustomer($operand->toId());
            $parameters['orders'] = $this->registry->repository(Order::class)
                ->findBy(['customerId' => $operand->toId()], ['id' => 'DESC'], 20)
            ;
            $parameters['payments'] = $this->registry->repository(CustomerTransactionView::class)
                ->findBy(['operandId' => $operand->toId()], ['id' => 'DESC'], 20)
            ;
            $parameters['notes'] = $this->registry->repository(NoteView::class)
                ->findBy(['subject' => $operand->toId()->toUuid()], ['id' => 'DESC'])
            ;
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }
}
