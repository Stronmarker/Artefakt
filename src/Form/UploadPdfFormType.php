<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UploadPdfFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'Upload PDF',
                'required' => true,
            ])
            ->add('dimensions', ChoiceType::class, [
                'label' => 'Choisissez les dimensions',
                'choices' => [
                    '84 mm x 55 mm' => '84x55',
                    '148 mm x 148 mm' => '148x148',
                    '105 mm x 148 mm' => '105x148',
                ],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
