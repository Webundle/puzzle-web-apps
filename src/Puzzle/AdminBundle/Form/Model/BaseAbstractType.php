<?php

namespace Puzzle\AdminBundle\Form\Model;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class BaseAbstractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('language', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'admin.property.language',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'class' => 'AdminBundle:Language',
                'choice_label' => 'name',
                'choice_translation_domain' => 'messages',
                'attr' => [
                    'data-md-selectize' => true
                ],
            ))
        ;
    }
    
}