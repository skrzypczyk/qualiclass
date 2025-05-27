<?php

namespace App\Form;

use App\Entity\Program;
use App\Entity\School;
use App\Entity\User;
use App\Entity\UserSchool;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSchoolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $owner = $options['owner'];
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getEmail();
                },
                'label' => 'Utilisateur',
                'query_builder' => function (EntityRepository $er) use ($owner) {
                    return $er->createQueryBuilder('u')
                        ->where('u.owner = :owner')
                        ->andWhere('u.isDisable = false OR u.isDisable IS NULL')
                        ->setParameter('owner', $owner);
                },
            ])
            ->add('school', EntityType::class, [
                'class' => School::class,
                'choice_label' => 'name',
                'label' => 'École',
                'query_builder' => function (EntityRepository $er) use ($owner) {
                    return $er->createQueryBuilder('s')
                        ->where('s.owner = :owner')
                        ->andWhere('s.isDisable = false OR s.isDisable IS NULL')
                        ->setParameter('owner', $owner);
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserSchool::class,
            'owner' => null, // 👈 ajoute l'option personnalisée "owner"
        ]);
    }
}
