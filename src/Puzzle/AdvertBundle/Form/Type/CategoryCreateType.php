<?php

namespace Puzzle\AdvertBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\AdvertBundle\Form\Model\AbstractCategoryType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class CategoryCreateType extends AbstractCategoryType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'category_create');
        $resolver->setDefault('validation_groups', ['Create']);
    }
    
    public function getBlockPrefix() {
        return 'admin_advert_category_create';
    }
}