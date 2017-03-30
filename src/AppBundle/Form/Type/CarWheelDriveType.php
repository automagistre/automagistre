<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Enum\CarWheelDrive;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class CarWheelDriveType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices'      => CarWheelDrive::all(),
            'choice_label' => 'name',
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
