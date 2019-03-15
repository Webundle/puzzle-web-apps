<?php

namespace Puzzle\CurriculumBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\CurriculumBundle\Form\Model\AbstractApplicantType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class ApplicantUpdateType extends AbstractApplicantType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder->remove('user');
    }
    
    
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'applicant_update');
        $resolver->setDefault('validation_groups', ['Update']);
    }
    
    public function getBlockPrefix() {
        return 'admin_curriculum_applicant_update';
    }
}