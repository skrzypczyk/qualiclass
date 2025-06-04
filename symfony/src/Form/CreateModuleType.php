<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Module;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Titre du module',
                    'class' => 'form-control',
                ],
            ])
            ->add('isShared', CheckboxType::class, [
                'label' => 'Module partagé',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])
            ->add('owner', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getEmail();
                },
                'label' => 'Référent pédagogique',
                'attr' => [
                    'placeholder' => 'Propriétaire du module',
                    'class' => 'form-control',
                ],
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('u')
                        ->where('u.school = :school')
                        ->andWhere('u.isDisable = false OR u.isDisable IS NULL')
                        ->setParameter('school', $user->getSchool())
                        ->orderBy('u.email', 'ASC');
                }
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée (en heures)',
                'attr' => [
                    'placeholder' => 'Durée du module',
                    'class' => 'form-control',
                ],
            ])
            ->add('credit', IntegerType::class, [
                'label' => 'Crédits',
                'attr' => [
                    'placeholder' => 'Exemple: crédits ECTS',
                    'class' => 'form-control',
                ],
            ])
            ->add('goal', TextareaType::class, [
                'label' => 'Objectifs',
                'attr' => [
                    'placeholder' => 'Objectifs du module',
                    'class' => 'form-control',
                ],
            ])
            ->add('syllabus', TextareaType::class, [
                'label' => 'Syllabus',
                'attr' => [
                    'placeholder' => 'Syllabus du module',
                    'class' => 'form-control',
                ],
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire',
                'attr' => [
                    'placeholder' => 'Commentaire sur le module',
                    'class' => 'form-control',
                ],
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'required' => false,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('c')
                        ->where('c.school = :school')
                        ->setParameter('school', $user->getSchool())
                        ->orderBy('c.name', 'ASC');
                },
                'label' => 'Catégories',
                'attr' => [
                    'placeholder' => 'Catégories du module',
                    'class' => 'form-control',
                ],
                'multiple' => true
            ])
            ->add('assessments', EntityType::class, [
                'class' => 'App\Entity\Assessment',
                'choice_label' => 'name',
                'label' => 'Évaluations',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('a')
                        ->where('a.school = :school')
                        ->setParameter('school', $user->getSchool())
                        ->orderBy('a.name', 'ASC');
                },
                'attr' => [
                    'placeholder' => 'Sélectionnez les évaluations associées',
                    'class' => 'form-control',
                ],
                'multiple' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Module::class,
            'user' => null
        ]);
    }
}
