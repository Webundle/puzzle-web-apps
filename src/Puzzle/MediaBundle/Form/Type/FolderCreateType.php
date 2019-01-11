<?php

namespace Puzzle\MediaBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\MediaBundle\Form\Model\AbstractFolderType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class FolderCreateType extends AbstractFolderType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'folder_create');
        $resolver->setDefault('validation_groups', ['Create']);
    }
    
    public function getBlockPrefix() {
        return 'admin_media_folder_create';
    }
}