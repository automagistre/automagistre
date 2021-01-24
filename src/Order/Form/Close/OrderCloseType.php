<?php

declare(strict_types=1);

namespace App\Order\Form\Close;

use App\Order\Entity\OrderStorage;
use App\Order\Form\Feedback\FeedbackType;
use App\Order\Form\Finish\OrderFinishType;
use App\Order\Form\Payment\OrderPaymentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function assert;

final class OrderCloseType extends AbstractType
{
    private OrderStorage $orderStorage;

    public function __construct(OrderStorage $orderStorage)
    {
        $this->orderStorage = $orderStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('finish', OrderFinishType::class)
            ->add('payment', OrderPaymentType::class, [
                'disabled_description' => true,
                'predefine_payment' => false,
            ])
            ->add('feedback', FeedbackType::class)
        ;

        $builder
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
                $data = $event->getData();
                assert($data instanceof OrderCloseDto);

                $order = $this->orderStorage->get($data->orderId);

                if ($order->getTotalForPayment()->isZero()) {
                    $event->getForm()->remove('payment');
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderCloseDto::class,
        ]);
    }
}
