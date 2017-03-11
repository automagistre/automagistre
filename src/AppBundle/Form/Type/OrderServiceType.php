<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Client;
use AppBundle\Entity\OrderService;
use AppBundle\Entity\Service;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use JavierEguiluz\Bundle\EasyAdminBundle\Form\Type\EasyAdminAutocompleteType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class OrderServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('service', EasyAdminAutocompleteType::class, [
                'class' => Service::class,
                'label' => 'Работа',
            ])
            ->add('cost', NumberType::class, [
                'label' => 'Цена',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('user')
                        ->join('user.person', 'person')
                        ->join(Client::class, 'client', Join::WITH, 'client.person = person')
                        ->where('client.employee = TRUE');
                },
                'choice_label' => 'fullName',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrderService::class,
        ]);
    }
}
