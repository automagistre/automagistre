<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Embeddable\OperandRelation;
use App\Entity\Landlord\Operand;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SellerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!(bool) $options['relational']) {
            return;
        }

        $builder->addModelTransformer(new CallbackTransformer(
            static function (?OperandRelation $relation) {
                return $relation->entityOrNull();
            },
            static function (?Operand $operand) {
                return null === $operand ? null : new OperandRelation($operand);
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'placeholder' => 'Выберите поставщика',
            'class' => Operand::class,
            'query_builder' => static function (EntityRepository $repository) {
                return $repository->createQueryBuilder('entity')
                    ->where('entity.seller = :is_seller')
                    ->setParameter('is_seller', true);
            },
            'choice_label' => static function (Operand $operand) {
                return (string) $operand;
            },
            'choice_value' => 'id',
            'relational' => false,
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
