<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Module;
use App\Entity\User;
use App\Form\Type\TrixType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
                        ->where('u.owner = :owner')
                        ->andWhere('u.isDisable = false OR u.isDisable IS NULL')
                        ->orWhere('u.id = :owner')
                        ->setParameter('owner', $user)
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
            ->add('goal', TrixType::class, [
                'label' => 'Objectifs',
                'attr' => [
                    'placeholder' => 'Objectifs du module',
                    'class' => 'form-control',
                ],
            ])
            ->add('syllabus', TrixType::class, [
                'label' => 'Syllabus',
                'attr' => [
                    'placeholder' => 'Syllabus du module',
                    'class' => 'form-control',
                ],
            ])
            ->add('comment', TrixType::class, [
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
                        ->leftJoin('c.school', 's')
                        ->leftJoin('s.users', 'u')
                        ->where('u = :user')
                        ->setParameter('user', $user)
                        ->orderBy('s.name', 'ASC')
                        ->addOrderBy('c.name', 'ASC');
                },
                'label' => 'Catégories',
                'attr' => [
                    'placeholder' => 'Catégories du module',
                    'class' => 'form-control',
                ],
                'multiple' => true,
                'group_by' => function ($category) {
                    return $category->getSchool() ? $category->getSchool()->getName() : 'Sans école';
                },
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
