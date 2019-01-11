<?php

namespace Puzzle\CalendarBundle\Form\Type;

use Puzzle\TranslationBundle\Form\Type\TranslationFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class MomentTranslationFormType extends TranslationFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('title', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'calendar.property.moment.title',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input slugglable'
                ],
            ])
            ->add('slug', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'calendar.property.moment.slug',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input slug'
                ],
            ])
            ->add('tags', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.property.moment.tags',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'data-role' => "materialtags"
                ],
                'required' => false
            ))
            ->add('description', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.property.moment.description',
                'attr' => [
                    'class' => 'md-input',
                    'title' => ""
                ],
            ))
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'messages',
                'label' => 'button.save',
                'attr' => [
                    'class' => "md-btn md-btn-success"
                ]
            ))
        ;
    }
}