<?php

namespace Puzzle\ContactBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\ContactBundle\Form\Model\AbstractContactType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class ContactCreateType extends AbstractContactType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'contact_create');
        $resolver->setDefault('validation_groups', ['Create']);
    }
    
    public function getBlockPrefix() {
        return 'admin_contact_create';
    }
}