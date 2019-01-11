<?php

namespace Puzzle\NewsletterBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Puzzle\NewsletterBundle\Entity\Template;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class TemplateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'newsletter.property.template.name',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input',
                    'title' => ""
                ],
            ])
            ->add('document', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'newsletter.property.template.document',
                'required' => false
            ))
            ->add('description', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'newsletter.property.template.description',
                'attr' => [
                    'class' => 'md-input',
                    'title' => ""
                ],
            ))
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'messages',
                'label' => 'button.save',
                'attr' => [
                    'class' => "md-fab md-fab-accent"
                ]
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