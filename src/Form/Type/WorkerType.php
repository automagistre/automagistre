<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Landlord\Operand;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class WorkerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'placeholder' => 'Выберите исполнителя',
            'class' => Operand::class,
            'query_builder' => function (EntityRepository $repository) {
                return $repository->createQueryBuilder('entity')
                    ->where('entity.contractor = :is_contractor')
                    ->setParameter('is_contractor', true);
            },
            'choice_label' => function (Operand $operand) {
                return (string) $operand;
            },
            'choice_value' => 'id',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return EntityType::class;
    }
}
