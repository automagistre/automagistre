<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Tenant\OrderItemService;
use LogicException;
use function sprintf;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class OrderItemServiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('worker', WorkerType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $item = $form->getData();
        if (!$item instanceof OrderItemService) {
            throw new LogicException(sprintf('Data must be instance of "%s"', OrderItemService::class));
        }

        $view->vars['label'] = $item->getService();

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
            ]);
    }
}
