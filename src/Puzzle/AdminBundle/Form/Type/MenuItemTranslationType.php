<?php

namespace Puzzle\AdminBundle\Form\Type;

use Puzzle\TranslationBundle\Form\Type\TranslationFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class MenuItemTranslationType extends TranslationFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'admin.property.page.name',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
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
    
    public function getBlockPrefix(){
        return 'admin_translate_menu_item';
    }
}