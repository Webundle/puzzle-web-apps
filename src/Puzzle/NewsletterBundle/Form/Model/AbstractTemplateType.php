<?php

namespace Puzzle\NewsletterBundle\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Puzzle\NewsletterBundle\Entity\Template;
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
                'label' => 'newsletter.template.name',
            ])
            ->add('event', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'newsletter.template.event',
                'required' => false
            ))
            ->add('content', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'newsletter.template.content',
                'required' => false
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