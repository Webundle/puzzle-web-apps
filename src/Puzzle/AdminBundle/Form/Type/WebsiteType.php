<?php

namespace Puzzle\AdminBundle\Form\Type;

use Puzzle\AdminBundle\Entity\Website;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class WebsiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'admin.property.setting.name',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
            ])
            ->add('email', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'admin.property.setting.email',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
            ])
            ->add('description', TextareaType::class, [
                'translation_domain' => 'messages',
                'label' => 'admin.property.setting.description',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
            ])
            ->add('dateFormat', ChoiceType::class, [
                'translation_domain' => 'messages',
                'choices' => [
                    'd-m-Y' => 'DD-MM-YYYY',
                    'm-d-Y' => 'MM-DD-YYYY',
                    'Y-m-d' => 'YYYY-MM-DD'
                ],
                'choice_label' => 'admin.property.setting.dateFormat',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input',
                ],
            ])
            ->add('timeFormat', ChoiceType::class, [
                'translation_domain' => 'messages',
                'choices' => [
                    'h:i' => '24h',
                    'h:i' => '12h',
                ],
                'choice_label' => 'admin.property.setting.dateFormat',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input',
                ],
            ])
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'messages',
                'label' => 'button.save',
                'attr' => [
                    'class' => "md-btn md-btn-success"
                ]
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Website::class,
            'validation_groups' => array(
                Website::class,
                'determineValidationGroups',
            ),
        ));
    }
}