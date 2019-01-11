<?php

namespace Puzzle\AdminBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\AdminBundle\Form\Model\AbstractMenuType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class MenuUpdateType extends AbstractMenuType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'menu_update');
        $resolver->setDefault('validation_groups', ['Update']);
    }
    
    public function getBlockPrefix() {
        return 'admin_menu_update';
    }
}