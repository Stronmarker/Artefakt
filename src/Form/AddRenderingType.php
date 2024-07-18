<?php

namespace App\Form;

use App\Entity\Rendering;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AddRenderingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        
        $builder
        ->add('frontPngFile', VichImageType::class, [
            'required' => false,
            'allow_delete' => true,
            'delete_label' => '...',
            'download_label' => '...',
            'download_uri' => true,
            'image_uri' => true,
            'asset_helper' => true,
        ])
        ->add('towardPngFile', VichImageType::class, [
            'required' => false,
            'allow_delete' => true,
            'delete_label' => '...',
            'download_label' => '...',
            'download_uri' => true,
            'image_uri' => true,
            'asset_helper' => true,
        ])
        // ->add('gildingFrontPngFile', VichImageType::class, [
        //     'required' => false,
        //     'allow_delete' => true,
        //     'delete_label' => '...',
        //     'download_label' => '...',
        //     'download_uri' => true,
        //     'image_uri' => true,
        //     'asset_helper' => true,
        // ])
        // ->add('gildingTowardPngFile', VichImageType::class, [
        //     'required' => false,
        //     'allow_delete' => true,
        //     'delete_label' => '...',
        //     'download_label' => '...',
        //     'download_uri' => true,
        //     'image_uri' => true,
        //     'asset_helper' => true,
        // ])
        // ->add('laminationFrontPngFile', VichImageType::class, [
        //     'required' => false,
        //     'allow_delete' => true,
        //     'delete_label' => '...',
        //     'download_label' => '...',
        //     'download_uri' => true,
        //     'image_uri' => true,
        //     'asset_helper' => true,
        // ])
        // ->add('laminationTowardPngFile', VichImageType::class, [
        //     'required' => false,
        //     'allow_delete' => true,
        //     'delete_label' => '...',
        //     'download_label' => '...',
        //     'download_uri' => true,
        //     'image_uri' => true,
        //     'asset_helper' => true,
        // ])
        
        
        ->add('rendering_name', TextType::class, ['label' => 'rendering_name']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rendering::class,
        ]);
    }
}
