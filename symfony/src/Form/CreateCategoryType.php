<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Module;
use App\Entity\School;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class
                , [
                    'label' => 'Nom de la catégorie',
                    'attr' => [
                        'placeholder' => 'Nom de la catégorie',
                        'class' => 'form-control',
                    ],
                ])
            ->add('school', EntityType::class, [
                'label' => 'Écoles',
                'class' => School::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
