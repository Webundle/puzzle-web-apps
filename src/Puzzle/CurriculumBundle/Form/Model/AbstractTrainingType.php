<?php

namespace Puzzle\CurriculumBundle\Form\Model;

use Puzzle\CurriculumBundle\Entity\Training;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class AbstractTrainingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.training.name'
            ])
            ->add('description', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'curriculum.training.description',
                'required' => false,
            ))
            ->add('startedAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.training.startedAt',
                'mapped' => false
            ])
            ->add('endedAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.training.endedAt',
                'mapped' => false
            ])
            ->add('school', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.training.school',
            ])
            ->add('address', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.training.address',
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Training::class,
            'validation_groups' => array(
                Training::class,
                'determineValidationGroups',
            ),
        ));
    }
}