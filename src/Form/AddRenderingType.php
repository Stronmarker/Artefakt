<?php

namespace App\Form;

use App\Entity\Rendering;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AddRenderingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
        ->add('frontPng', FileType::class, ['label' => 'Front PNG'])
        ->add('towardPng', FileType::class, ['label' => 'Toward PNG'])
        ->add('gildingSvg', FileType::class, ['label' => 'Gilding SVG', 'required' => false])
        ->add('laminationSvg', FileType::class, ['label' => 'Lamination SVG', 'required' => false])
        ->add('dimensions', ChoiceType::class, [
            'label' => 'Dimensions',
            'choices' => [
                '84x55' => '84x55',
                '148x148' => '148x148',
                '148x105' => '148x105',
            ],
        ])
        ->add('link', TextType::class, ['label' => 'Link', 'required' => false])
        ->add('rendering_name', TextType::class, ['label' => 'rendering_name']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rendering::class,
        ]);
    }
}
