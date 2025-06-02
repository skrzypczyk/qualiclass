<?php
namespace App\Form;

use App\Entity\Subscription;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isFreeAccount = $options['isFreeAccount'] ?? false;
        $limitUsers = $options['limitUsers'] ?? null;

        $builder
            ->add('isFreeAccount', CheckboxType::class, [
                'label' => 'Compte gratuit',
                'data' => $isFreeAccount,
                'required' => false,
                'mapped' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Super Administrateur' => 'ROLE_SUPER_ADMIN',
                    'Administrateur' => 'ROLE_ADMIN',
                    'Utilisateur' => 'ROLE_USER',
                ],
                'multiple' => true,
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => false, // facultatif si tu ne veux pas le changer
                'empty_data' => '',
                'mapped' => false, // important !
            ])
            ->add('isVerified', CheckboxType::class, [
                'label' => 'Email vérifié',
                'required' => false,
            ])
            ->add('firstname', null, [
                'label' => 'Prénom',
            ])
            ->add('lastname', null, [
                'label' => 'Nom',
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
            ])
            ->add('limitUsers', IntegerType::class, [
                'label' => 'Surcharge du nombre d\'utilisateurs',
                'required' => false,
                'data' => $limitUsers,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'isFreeAccount' => false, // pour le champ isFreeAccount
            'limitUsers' => null, // pour le champ limitUsers
        ]);
    }
}
