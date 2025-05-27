<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Type\TrixType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('schoolName', TextType::class, [
                'label' => 'Nom de l\'école',
                'attr' => [
                    'placeholder' => 'Nom de l\'école',
                    'class' => 'form-control',
                ],
            ])
            ->add('schoolDescription', TrixType::class, [
                'label' => 'Description de l\'école',
                'attr' => [
                    'placeholder' => 'Description de l\'école',
                    'class' => 'form-control',
                ],
            ])
            ->add('schoolImg', FileType::class, [
                'label' => 'Logo de l\'école',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Logo de l\'école',
                    'class' => 'form-control',
                    'accept' => 'image/*',
                ],

            ])
            ->add('schoolPrimaryColor', ColorType::class, [
                'label' => 'Couleur primaire de l\'école',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('schoolSecondaryColor', ColorType::class, [
                'label' => 'Couleur du texte',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('schoolTypo', ChoiceType::class, [
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
            'data_class' => User::class,
        ]);
    }
}
