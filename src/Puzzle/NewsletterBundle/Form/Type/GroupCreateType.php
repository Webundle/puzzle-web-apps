<?php

namespace Puzzle\NewsletterBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\NewsletterBundle\Form\Model\AbstractGroupType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class GroupCreateType extends AbstractGroupType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'newsletter_group_create');
        $resolver->setDefault('validation_groups', ['Create']);
    }
    
    public function getBlockPrefix() {
        return 'admin_newsletter_group_create';
    }
}