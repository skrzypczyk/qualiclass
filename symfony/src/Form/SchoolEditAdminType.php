<?php

namespace App\Form;

use App\Entity\School;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolEditAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'école',
                'attr' => ['placeholder' => 'Entrez le nom de l\'école'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de l\'école',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Description de l\'école',
                    'class' => 'form-control',
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
            ->add('limitUsers', NumberType::class, [
                'label' => 'Nombre maximum d\'utilisateurs',
                'attr' => ['placeholder' => 'Entrez le nombre maximum d\'utilisateurs'],
                'required' => false,
            ])
            ->add('isFreeAccount', CheckboxType::class, [
                'label' => 'Compte gratuit',
                'required' => false,
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
