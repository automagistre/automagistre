<?php

declare(strict_types=1);

namespace App\Order\Form\Payment;

use App\Balance\Entity\BalanceView;
use App\Customer\Entity\Operand;
use App\EasyAdmin\Form\AutocompleteType;
use App\Order\Entity\Order;
use App\Shared\Doctrine\Registry;
use App\Wallet\Entity\Wallet;
use function array_map;
use function assert;
use Money\Money;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class OrderPaymentType extends AbstractType
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recipient', AutocompleteType::class, [
                'label' => 'Получатель',
                'class' => Operand::class,
                'disabled' => true,
            ])
            ->add('description', TextType::class, [
                'label' => 'Комментарий',
                'required' => false,
            ])
            ->add('wallets', CollectionType::class, [
                'entry_type' => OrderPaymentWalletType::class,
            ]);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $dto = $event->getData();
                assert($dto instanceof OrderPaymentDto);

                /** @var Order $order */
                $order = $this->registry->get(Order::class, $dto->orderId);

                $customerId = $order->getCustomerId();
                $balance = null === $customerId
                    ? null
                    : $this->registry->get(BalanceView::class, $customerId)->money;

                $forPayment = $order->getTotalForPayment($balance);

                $dto->payment = $forPayment->isPositive() ? $forPayment : new Money(0, $forPayment->getCurrency());
                $dto->recipient = $customerId;
            }, 1)
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $dto = $event->getData();
                assert($dto instanceof OrderPaymentDto);

                if ([] !== $dto->wallets) {
                    return;
                }

                $payment = $dto->payment;
                $dto->wallets = array_map(
                    static function (Wallet $wallet) use (&$payment): OrderPaymentWalletDto {
                        [$money, $payment] = [$payment, $payment->multiply(0)];

                        return new OrderPaymentWalletDto(
                            $wallet->toId(),
                            $money,
                        );
                    },
                    $this->registry->manager()->getRepository(Wallet::class)->findBy(['useInOrder' => true]),
                );
            }, 0);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderPaymentDto::class,
            'label' => false,
            'constraints' => [
                new Assert\Callback(
                    static function (OrderPaymentDto $dto, ExecutionContextInterface $context): void {
                        /** @var Money|null $money */
                        $money = null;
                        foreach ($dto->wallets as $walletDto) {
                            $money = null === $money ? $walletDto->payment : $money->add($walletDto->payment);
                        }

                        if (null !== $money && !$money->isPositive()) {
                            $context->addViolation('Сумма должна быть положительной');
                        }
                    }
                ),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'order_payment';
    }
}
