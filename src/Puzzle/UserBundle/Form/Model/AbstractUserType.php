<?php

namespace Puzzle\UserBundle\Form\Model;

use Puzzle\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('firstName', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.property.user.firstName'
            ])
            ->add('lastName', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.property.user.lastName'
            ])
            ->add('email', EmailType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.property.user.email'
            ])
            ->add('phoneNumber', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.property.user.phoneNumber',
                'required' => false
            ])
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'user.property.user.picture',
                'required' => false
            ))
            ->add('username', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.property.user.username',
            ])
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'options' => ['required' => false],
                'first_options'  => [
                    'translation_domain' => 'messages',
                    'label' => 'user.property.user.password'
                ],
                'second_options'  => [
                    'translation_domain' => 'messages',
                    'label' => 'user.property.user.password_repeated'
                ],
                'required' => false
            ))
            ->add('credentialsExpiredAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.property.user.credentialsExpiredAt',
                'mapped' => false,
                'required' => false
            ])
            ->add('accountExpiredAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.property.user.accountExpiredAt',
                'mapped' => false,
                'required' => false
            ])
            ->add('enabled', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'user.property.user.enabled',
                'required' => false,
                'mapped' => false,
            ))
            ->add('locked', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'user.property.user.locked',
                'required' => false,
                'mapped' => false,
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'validation_groups' => array(
                User::class,
                'determineValidationGroups',
            ),
        ));
    }
}