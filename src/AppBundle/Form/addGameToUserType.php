<?php

namespace AppBundle\Form;

use AppBundle\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class addGameToUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->
        add('game', EntityType::class, [
            'attr' => ['data-select' => 'true'],
            'class' => 'AppBundle:Game',
            'choice_label' => function ($game) {
                /**
                 * @var Game $game
                 */
                return $game->getName();
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
        return 'test';
    }
}
