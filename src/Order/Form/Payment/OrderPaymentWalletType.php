<?php

declare(strict_types=1);

namespace App\Order\Form\Payment;

use App\EasyAdmin\Form\AutocompleteType;
use App\Form\Type\MoneyType;
use App\Wallet\Entity\Wallet;
use Money\Money;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class OrderPaymentWalletType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('payment', MoneyType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Callback(static function (Money $money, ExecutionContextInterface $context): void {
                        if ($money->isNegative()) {
                            $context->addViolation('Сумма не может быть отрицательной!');
                        }
                    }),
                ],
            ])
            ->add('walletId', AutocompleteType::class, [
                'label' => 'Счет',
                'class' => Wallet::class,
                'disabled' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderPaymentWalletDto::class,
        ]);
    }
}
