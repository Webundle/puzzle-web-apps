<?php

namespace Puzzle\ContactBundle\Form\Model;

use Puzzle\ContactBundle\Entity\Contact;
use Puzzle\ContactBundle\Entity\Group;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.group.name'
            ])
            ->add('description', TextareaType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.group.description',
                'required' => false
            ])
            ->add('contacts', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'contact.property.group.contacts',
                'class' => Contact::class,
                'choice_label' => 'fullName',
                'multiple' => true,
                'required' => false
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Group::class,
            'validation_groups' => array(
                Group::class,
                'determineValidationGroups',
            ),
        ));
    }
}