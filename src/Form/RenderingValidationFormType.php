<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RenderingValidationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isAccepted', ChoiceType::class, [
                'choices' => [
                    'Valider' => true,
                    'Rejeter' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Validez-vous ce rendu ?',
            ])
            ->add('rejectReason', TextareaType::class, [
                'required' => false,
                'label' => 'Raison du rejet (si rejeté)',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
