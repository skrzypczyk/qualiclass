<?php

namespace App\Form;

use App\Entity\Diploma;
use App\Entity\School;
use App\Entity\User;
use App\Form\Type\TrixType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateDiplomaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Titre du diplôme',
                    'class' => 'form-control',
                ],
            ])
            ->add('RNCP', TextType::class, [
                'label' => 'Numéro RNCP',
                'attr' => [
                    'placeholder' => 'Numéro RNCP',
                    'class' => 'form-control',
                ],
            ])
            ->add('content', TrixType::class, [
                'label' => 'Détails du diplôme',
                'attr' => [
                    'placeholder' => 'Détails du diplôme',
                    'class' => 'form-control',
                ],
            ])->add('competences', CollectionType::class, [
                'entry_type' => CreateCompetenceType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => false,
            ])
            ->add('schools', EntityType::class, [
                'label' => 'Écoles',
                'class' => School::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Diploma::class,
        ]);
    }
}
