<?php

namespace App\Form;

use App\Entity\Rendering;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditRenderingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('renderingName', TextType::class, [
                'label' => 'Nom du rendu'
            ])
            ->add('frontPngFile', FileType::class, [
                'label' => 'Image avant (PNG)',
                'required' => false,
            ])
            ->add('towardPngFile', FileType::class, [
                'label' => 'Image arrière (PNG)',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rendering::class,
        ]);
    }
}
