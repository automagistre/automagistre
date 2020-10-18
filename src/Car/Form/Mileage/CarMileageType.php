<?php

declare(strict_types=1);

namespace App\Car\Form\Mileage;

use App\Car\Entity\Car;
use App\Shared\Doctrine\Registry;
use function sprintf;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CarMileageType extends AbstractType
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
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                /** @var CarMileageDto|null $data */
                $data = $event->getData();
                if (null === $data) {
                    return;
                }

                $form = $event->getForm();

                /** @var Car $car */
                $car = $this->registry->get(Car::class, $data->carId);
                $mileage = $car->mileage;

                $form->add('mileage', IntegerType::class, [
                    'label' => 'Пробег '.(0 === $mileage
                            ? '(предыдущий отсутствует)'
                            : sprintf('(предыдущий: %s)', $mileage)),
                ]);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CarMileageDto::class,
        ]);
    }
}
