<?php

namespace AppBundle\Form;

use const false;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\PlayLog;
use AppBundle\Entity\Expansion;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class PlayLogType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'date-picker form-control'
                ],
                'label' => 'Choose date ',
                'format' => 'MM/yyyy'
            ));

        $builder
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false

            ]);

        /** var Expansion $expansion */
        $builder->
            add('expansions', EntityType::class, [
                'attr' => ['data-select' => 'true'],

                'class' => 'AppBundle:Expansion',
                'choice_label' => function ($expansion) {
                    return $expansion->getName();
                },
                'multiple' => true,
                'expanded' => false,
                'required' => false
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PlayLog::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_playlog';
    }


}
