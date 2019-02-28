<?php

namespace Puzzle\CharityBundle\Form\Model;

use Puzzle\CharityBundle\Entity\Member;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\CharityBundle\Entity\Cause;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Puzzle\UserBundle\Entity\User;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class AbstractMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('firstName', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'charity.member.firstName',
                'required' => false
            ])
            ->add('lastName', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'charity.member.lastName',
                'required' => false
            ])
            ->add('phoneNumber', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'charity.member.phoneNumber'
            ])
            ->add('email', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'charity.member.email'
            ])
            ->add('user', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.member.user',
                'class' => User::class,
                'required' => false,
                'choice_label' => 'fullName'
            ))
            ->add('enabled', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.member.enabled.title',
                'required' => false
            ))
            ->add('createAccount', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.member.createAccount',
                'required' => false,
                'mapped' => false
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Member::class,
            'validation_groups' => array(
                Member::class,
                'determineValidationGroups',
            ),
        ));
    }
}