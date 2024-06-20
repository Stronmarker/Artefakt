<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProjectClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Project fields
            ->add('project_name', TextType::class, [
                'label' => 'Project Name'
            ])
            ->add('creation_date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Creation Date'
            ])
            ->add('modification_date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Modification Date'
            ])
            // Client fields
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'client_name',
                'label' => 'Client',
                'mapped' => false,
                'required' => false
            ])
            ->add('client_name', TextType::class, [
                'label' => 'Client Name',
                'mapped' => false
            ])
            ->add('client_email', EmailType::class, [
                'label' => 'Client Email',
                'mapped' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
