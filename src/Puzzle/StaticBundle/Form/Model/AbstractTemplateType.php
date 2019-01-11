<?php

namespace Puzzle\StaticBundle\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Puzzle\StaticBundle\Entity\Template;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'static.property.template.name',
            ])
            ->add('content', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'static.property.template.content'
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Template::class,
            'validation_groups' => array(
                Template::class,
                'determineValidationGroups',
            ),
        ));
    }
}