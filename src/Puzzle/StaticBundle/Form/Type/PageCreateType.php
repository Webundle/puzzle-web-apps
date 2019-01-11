<?php

namespace Puzzle\StaticBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\StaticBundle\Form\Model\AbstractPageType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class PageCreateType extends AbstractPageType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'page_create');
        $resolver->setDefault('validation_groups', ['Create']);
    }
    
    public function getBlockPrefix() {
        return 'admin_static_page_create';
    }
}