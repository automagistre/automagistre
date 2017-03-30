<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Enum\Carcase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class CarcaseType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices'      => Carcase::all(),
            'choice_label' => 'name',
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
