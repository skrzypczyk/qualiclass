<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateModuleWithChatgptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du module',
                'attr' => ['placeholder' => 'Entrez le titre du module'],
            ])
            ->add('duration', NumberType::class, [
                'label' => 'Durée du module (en heures)',
                'attr' => ['placeholder' => 'Entrez la durée du module'],
                'required' => true,
            ])
            ->add('nbSessions', NumberType::class, [
                'label' => 'Nombre de séances',
                'attr' => ['placeholder' => 'Entrez le nombre de séances'],
                'required' => false,
            ])
            ->add('level', TextType::class, [
                'label' => 'Niveau du module',
                'attr' => ['placeholder' => 'Entrez le niveau du module (débutant, intermédiaire, avancé)'],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
