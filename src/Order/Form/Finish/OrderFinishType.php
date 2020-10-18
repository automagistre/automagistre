<?php

declare(strict_types=1);

namespace App\Order\Form\Finish;

use App\Car\Form\Mileage\CarMileageType;
use App\Order\Entity\OrderStorage;
use function assert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OrderFinishType extends AbstractType
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
            ->add('services', CollectionType::class, [
                'entry_type' => OrderItemServiceWorkerType::class,
                'label' => false,
            ])
            ->add('mileage', CarMileageType::class, [
                'label' => false,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $dto = $event->getData();
                assert($dto instanceof OrderFinishDto);

                $order = $this->orderStorage->get($dto->orderId);
                $dto->services = $order->getServicesWithoutWorker();

                if ([] === $dto->services) {
                    $event->getForm()->remove('services');
                }
            })
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $form = $event->getForm();
                $dto = $event->getData();
                assert($dto instanceof OrderFinishDto);

                $order = $this->orderStorage->get($dto->orderId);
                $mileage = $order->getMileage();

                if (null !== $mileage) {
                    $form->remove('mileage');

                    $mileageDto = $dto->mileage;
                    if (null !== $mileageDto) {
                        $mileageDto->mileage = $mileage;
                    }
                }

                if (null === $dto->mileage) {
                    $form->remove('mileage');
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderFinishDto::class,
            'label' => false,
        ]);
    }
}
