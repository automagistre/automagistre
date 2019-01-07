<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Costil;
use App\Entity\Wallet;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class WalletType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'class' => Wallet::class,
                'query_builder' => function (EntityRepository $repository) {
                    return $repository
                        ->createQueryBuilder('entity')
                        ->where('entity.owner = :id')
                        ->orderBy('entity.id', 'ASC')
                        ->setParameter('id', Costil::MY_ORGANIZATION_ID);
                },
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
