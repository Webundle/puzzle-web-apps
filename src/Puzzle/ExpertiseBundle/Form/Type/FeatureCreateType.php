<?php

namespace Puzzle\ExpertiseBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\ExpertiseBundle\Form\Model\AbstractFeatureType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class FeatureCreateType extends AbstractFeatureType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'feature_create');
        $resolver->setDefault('validation_groups', ['Create']);
    }
    
    public function getBlockPrefix() {
        return 'admin_expertise_feature_create';
    }
}