<?php

namespace App\Form;

use App\Entity\School;
use App\Form\Type\TrixType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'école',
                'attr' => [
                    'placeholder' => 'Nom de l\'école',
                    'class' => 'form-control',
                ],
            ])
            ->add('description', TrixType::class, [
                'label' => 'Description de l\'école',
                'attr' => [
                    'placeholder' => 'Description de l\'école',
                    'class' => 'form-control',
                ],
            ])
            ->add('img', FileType::class, [
                'label' => 'Logo de l\'école',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Logo de l\'école',
                    'class' => 'form-control',
                    'accept' => 'image/*',
                ],

            ])
            ->add('primaryColor', ColorType::class, [
                'label' => 'Couleur primaire de l\'école',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('secondaryColor', ColorType::class, [
                'label' => 'Couleur du texte',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('typo', ChoiceType::class, [
                'label' => 'Typographie de l\'école',
                'choices' => [
                    'Arial' => 'arial',
                    'Verdana' => 'verdana',
                    'Georgia' => 'georgia',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => School::class,
        ]);
    }
}
