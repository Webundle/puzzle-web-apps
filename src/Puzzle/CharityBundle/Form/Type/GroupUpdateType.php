<?php

namespace Puzzle\CharityBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\CharityBundle\Form\Model\AbstractGroupType;

/**
 *
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 *
 */
class GroupUpdateType extends AbstractGroupType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'charity_group_update');
        $resolver->setDefault('validation_groups', ['Update']);
    }
    
    public function getBlockPrefix() {
        return 'admin_charity_group_update';
    }
}