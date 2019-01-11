<?php

namespace Puzzle\LearningBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\LearningBundle\Form\Model\AbstractPostType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class PostCreateType extends AbstractPostType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'post_create');
        $resolver->setDefault('validation_groups', ['Create']);
    }
    
    public function getBlockPrefix() {
        return 'admin_learning_post_create';
    }
}