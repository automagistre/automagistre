<?php

declare(strict_types=1);

namespace App\Order\Form\Finish;

use App\Customer\Form\WorkerType;
use App\Order\Entity\OrderItemService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderItemServiceWorkerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('workerId', WorkerType::class, [
                'label' => false,
                'required' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $dto = $form->getData();
        assert($dto instanceof OrderItemService);

        $view->vars['label'] = $dto->service;

        parent::buildView($view, $form, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => OrderItemService::class,
            ])
        ;
    }
}
