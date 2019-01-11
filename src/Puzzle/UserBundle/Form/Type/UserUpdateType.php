<?php

namespace Puzzle\UserBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\UserBundle\Form\Model\AbstractUserType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class UserUpdateType extends AbstractUserType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'user_update');
        $resolver->setDefault('validation_groups', ['Update']);
    }
    
    public function getBlockPrefix() {
        return 'admin_user_update';
    }
}