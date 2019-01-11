<?php

namespace Puzzle\ContactBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\ContactBundle\Form\Model\AbstractGroupType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class GroupCreateType extends AbstractGroupType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'contact_group_create');
        $resolver->setDefault('validation_groups', ['Create']);
    }
    
    public function getBlockPrefix() {
        return 'admin_contact_group_create';
    }
}