<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Operand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SellerType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $em = $this->em;

        $resolver->setDefaults([
            'label' => false,
            'placeholder' => 'Выберите поставщика',
            'choice_loader' => new CallbackChoiceLoader(function () use ($em) {
                return $em->createQueryBuilder()
                    ->select('entity')
                    ->from(Operand::class, 'entity')
                    ->where('entity.seller = :is_seller')
                    ->setParameter('is_seller', true)
                    ->getQuery()
                    ->getResult();
            }),
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
        return ChoiceType::class;
    }
}
