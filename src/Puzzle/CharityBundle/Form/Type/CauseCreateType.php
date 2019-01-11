<?php

namespace Puzzle\CharityBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\CharityBundle\Form\Model\AbstractCauseType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class CauseCreateType extends AbstractCauseType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'cause_create');
        $resolver->setDefault('validation_groups', ['Create']);
    }
    
    public function getBlockPrefix() {
        return 'admin_charity_cause_create';
    }
}