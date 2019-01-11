<?php

namespace Puzzle\UserBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\UserBundle\Form\Model\AbstractUserType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class UserCreateType extends AbstractUserType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'user_create');
        $resolver->setDefault('validation_groups', ['Create']);
    }
    
    public function getBlockPrefix() {
        return 'admin_user_create';
    }
}