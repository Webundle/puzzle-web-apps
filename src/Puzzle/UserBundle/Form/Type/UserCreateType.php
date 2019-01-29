<?php

namespace Puzzle\UserBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\UserBundle\Form\Model\AbstractUserType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class UserCreateType extends AbstractUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder->remove('picture');
    }
    
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'user_create');
        $resolver->setDefault('validation_groups', ['Create']);
    }
    
    public function getBlockPrefix() {
        return 'admin_user_create';
    }
}