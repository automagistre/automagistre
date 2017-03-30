<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Enum\CarTransmission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class CarTransmissionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices'      => CarTransmission::all(),
            'choice_label' => 'name',
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
