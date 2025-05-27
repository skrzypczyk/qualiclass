<?php

namespace App\Form;

use App\Entity\Diploma;
use App\Entity\Program;
use App\Form\Type\TrixType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'] ?? null;
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Titre du programme'],
                'required' => true,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\NotBlank([
                        'message' => 'Le titre ne peut pas être vide.',
                    ]),
                    new \Symfony\Component\Validator\Constraints\Length([
                        'max' => 255,
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('year', IntegerType::class, [
                'label' => 'Année après le bac',
                'attr' => ['placeholder' => '1,2,3, ... , 10'],
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\Range([
                        'min' => 1,
                        'max' => 10,
                        'notInRangeMessage' => 'Veuillez sélectionner une valeur entre 1 et 10.',
                    ]),
                ],
            ])
            ->add('prerequisites',TrixType::class, [
                'label' => 'Prérequis',
                'attr' => ['placeholder' => 'Prérequis pour le programme'],
            ])
            ->add('goals', TrixType::class, [
                'label' => 'Objectifs',
                'attr' => ['placeholder' => 'Objectifs du programme'],
            ])
            ->add('notes', TrixType::class, [
                'label' => 'Notes',
                'attr' => ['placeholder' => 'Notes importantes concernant le programme'],
                'required' => false,
            ])
            ->add('diplomas', EntityType::class, [
                'label' => 'Diplômes associés',
                'class' => Diploma::class,
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.title', 'ASC');
                },
                'choice_label' => function (Diploma $diploma) {
                    return $diploma->getTitle() . ' (' . $diploma->getRNCP() . ')';
                },
                'multiple' => true,
            ])
            ->add('owner', EntityType::class, [
                'class' => 'App\Entity\User',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('u')
                        ->where('u.id = :userId')
                        ->setParameter('userId', $user ? $user->getId() : null)
                        ->orderBy('u.email', 'ASC');
                },
                'choice_label' => 'email',
                'label' => 'Propriétaire',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
            'user' => null,
        ]);
    }
}
