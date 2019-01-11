<?php

namespace Puzzle\ExpertiseBundle\Form\Model;

use Puzzle\ExpertiseBundle\Entity\Staff;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\ExpertiseBundle\Entity\Service;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractStaffType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('firstName', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.property.staff.firstName'
            ])
            ->add('lastName', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.property.staff.lastName'
            ])
            ->add('service', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'expertise.property.staff.service',
                'class' => Service::class,
                'choice_label' => 'name',
            ))
            ->add('position', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.property.staff.position',
            ])
            ->add('ranking', IntegerType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.property.staff.ranking',
            ])
            ->add('email', EmailType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.property.staff.email',
                'required' => false
            ])
            ->add('phoneNumber', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.property.staff.phoneNumber',
                'required' => false
            ])
            ->add('facebook', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.property.staff.facebook',
                'required' => false
            ])
            ->add('twitter', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.property.staff.twitter',
                'required' => false
            ])
            ->add('googlePlus', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.property.staff.googlePlus',
                'required' => false
            ])
            ->add('linkedIn', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.property.staff.linkedIn',
                'required' => false
            ])
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'expertise.property.staff.picture',
                'required' => false,
                'mapped' => false
            ))
            ->add('description', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'expertise.property.staff.description',
                'required' => false,
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => Staff::class,
            'validation_groups' => array(
                Staff::class,
                'determineValidationGroups',
            ),
        ));
    }
}