<?php

namespace Puzzle\UserBundle\Form\Model;

use Puzzle\UserBundle\Entity\User;
use Puzzle\UserBundle\Entity\Group;
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
                'label' => 'user.group.name'
            ])
            ->add('description', TextareaType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.group.description',
                'required' => false
            ])
            ->add('users', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'user.group.users',
                'class' => User::class,
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