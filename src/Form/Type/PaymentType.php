<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Operand;
use App\Form\Model\Payment;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminAutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recipient', EasyAdminAutocompleteType::class, [
                'class' => Operand::class,
                'label' => 'Получатель',
                'disabled' => $options['disable_recipient'],
            ])
            ->add('paymentType', ChoiceType::class, [
                'label' => 'Тип оплаты',
                'choices' => [
                    'Наличные' => 'cash',
                    'Безналичный' => 'non-cash',
                ],
                'multiple' => false,
                'expanded' => true,
                'required' => true,
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'Сумма',
            ])
            ->add('description', TextType::class, [
                'label' => 'Описание',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => 'Платёж',
                'data_class' => Payment::class,
                'disable_recipient' => false,
            ]);
    }
}
