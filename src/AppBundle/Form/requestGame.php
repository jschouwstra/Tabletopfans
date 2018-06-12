<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class requestGame extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('feedback', null, array(
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'e.g Monopoly My little pony'
                    ],
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getBlockPrefix()
    {
        return 'app_bundlerequest_game';
    }
}
