<?php

namespace Puzzle\ExpertiseBundle\Form\Model;

use Puzzle\ExpertiseBundle\Entity\Feature;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractFeatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.feature.name',
            ])
            ->add('description', TextareaType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.feature.description',
                'required' => false
            ])
            ->add('icon', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.feature.icon',
                'required' => false
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Feature::class,
            'validation_groups' => array(
                Feature::class,
                'determineValidationGroups',
            ),
        ));
    }
}