<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class addExpansionToGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** Expansion $expansion */
        $builder->add('expansion', EntityType::class, [
            'attr' => ['data-select' => 'true'],

            'class' => 'AppBundle:Expansion',
            'choice_label' => function ($expansion) {
                return $expansion->getName();
            },
            'multiple' => true,
            'expanded' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getBlockPrefix()
    {
        return 'app_bundleadd_expansion_to_game';
    }
}
