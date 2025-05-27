<?php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrixType extends AbstractType
{
    public function getParent(): string
    {
        // Trix utilise un input hidden
        return HiddenType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'trix';
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        // On passe l'ID à utiliser dans l'attribut input de <trix-editor>
        $view->vars['attr']['data-trix-id'] = $view->vars['id'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Aucune config spéciale à ce stade
    }
}
