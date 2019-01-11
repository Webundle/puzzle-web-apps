<?php

namespace Puzzle\StaticBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\StaticBundle\Form\Model\AbstractTemplateType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class TemplateCreateType extends AbstractTemplateType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'template_create');
        $resolver->setDefault('validation_groups', ['Create']);
    }
    
    public function getBlockPrefix() {
        return 'admin_static_template_create';
    }
}