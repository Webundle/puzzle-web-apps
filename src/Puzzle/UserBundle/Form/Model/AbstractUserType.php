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
                'label' => 'user.account.firstName'
            ])
            ->add('lastName', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.account.lastName'
            ])
            ->add('email', EmailType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.account.email'
            ])
            ->add('phoneNumber', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.account.phoneNumber',
                'required' => false
            ])
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'user.account.picture',
                'required' => false
            ))
            ->add('username', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.account.username',
            ])
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'options' => ['required' => false],
                'first_options'  => [
                    'translation_domain' => 'messages',
                    'label' => 'user.account.password'
                ],
                'second_options'  => [
                    'translation_domain' => 'messages',
                    'label' => 'user.account.password_confirmation'
                ],
                'required' => false
            ))
            ->add('credentialsExpiresAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.account.credentialsExpiresAt',
                'mapped' => false,
                'required' => false
            ])
            ->add('accountExpiresAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.account.accountExpiresAt',
                'mapped' => false,
                'required' => false
            ])
            ->add('enabled', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'user.account.enabled',
                'required' => false,
                'mapped' => false,
            ))
            ->add('locked', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'user.account.locked',
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