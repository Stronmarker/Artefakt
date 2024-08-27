<?php

namespace App\Form;

use App\Entity\Feedback;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackFormType extends AbstractType
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
                'attr' => ['class' => 'custom-radio-group']
            ])
        
            ->add('comment', TextareaType::class, [
                'required' => false,
                'label' => 'Commentaire',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class, // Spécifie que ce formulaire est associé à l'entité Feedback
        ]);
    }
}
