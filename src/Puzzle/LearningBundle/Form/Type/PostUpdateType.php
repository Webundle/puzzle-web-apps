<?php

namespace Puzzle\LearningBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\LearningBundle\Form\Model\AbstractPostType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class PostUpdateType extends AbstractPostType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'post_update');
        $resolver->setDefault('validation_groups', ['Update']);
    }
    
    public function getBlockPrefix() {
        return 'admin_learning_post_update';
    }
}