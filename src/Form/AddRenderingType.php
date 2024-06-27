<?php

namespace App\Form;

use App\Entity\Rendering;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AddRenderingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('frontPng', FileType::class, ['label' => 'Front PNG'])
            ->add('towardPng', FileType::class, ['label' => 'Toward PNG'])
            ->add('gildingSvg', FileType::class, ['label' => 'Gilding SVG', 'required' => false])
            ->add('laminationSvg', FileType::class, ['label' => 'Lamination SVG', 'required' => false])
            ->add('link', TextType::class, ['label' => 'Link']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rendering::class,
        ]);
    }
}
