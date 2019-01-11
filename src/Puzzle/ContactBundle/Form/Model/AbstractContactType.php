<?php

namespace Puzzle\ContactBundle\Form\Model;

use Puzzle\ContactBundle\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('firstName', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.contact.firstName'
            ])
            ->add('lastName', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.contact.lastName'
            ])
            ->add('email', EmailType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.contact.email'
            ])
            ->add('phoneNumber', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.contact.phoneNumber',
                'required' => false
            ])
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'contact.property.contact.picture',
                'required' => false
            ))
            ->add('company', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.contact.company',
                'required' => false
            ])
            ->add('position', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.contact.position',
                'required' => false
            ])
            ->add('location', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.contact.location',
                'required' => false
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Contact::class,
            'validation_groups' => array(
                Contact::class,
                'determineValidationGroups',
            ),
        ));
    }
}