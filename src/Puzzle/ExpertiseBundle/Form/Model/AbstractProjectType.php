<?php

namespace Puzzle\ExpertiseBundle\Form\Model;

use Puzzle\ExpertiseBundle\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\ExpertiseBundle\Entity\Service;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.project.name',
            ])
            ->add('description', TextareaType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.project.description',
            ])
            ->add('service', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'expertise.project.service',
                'class' => Service::class,
                'choice_label' => 'name'
            ))
            ->add('startedAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.project.startedAt'
            ])
            ->add('endedAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.project.endedAt'
            ])
            ->add('client', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.project.client'
            ])
            ->add('location', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.project.location',
                'required' => false
            ])
            ->add('pictures', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'expertise.project.pictures',
                'required' => false,
                'mapped' => false
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Project::class,
            'validation_groups' => array(
                Project::class,
                'determineValidationGroups',
            ),
        ));
    }
}