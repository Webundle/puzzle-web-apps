<?php

namespace Puzzle\ExpertiseBundle\Form\Model;

use Puzzle\ExpertiseBundle\Entity\Partner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractPartnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.partner.name'
            ])
            ->add('location', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.partner.location',
                'required' => false
            ])
            ->add('tags', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'expertise.partner.tags',
                'required' => false
            ))
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'expertise.partner.picture',
                'required' => false,
                'mapped' => false
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Partner::class,
            'validation_groups' => array(
                Partner::class,
                'determineValidationGroups',
            ),
        ));
    }
}