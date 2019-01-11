<?php

namespace Puzzle\AdminBundle\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class BaseBlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'admin.blog.property.post.name',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input slugglable'
                ],
            ])
            ->add('slug', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'admin.blog.property.post.slug',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input slug'
                ],
            ])
            ->add('description', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'admin.blog.property.post.description',
                'attr' => [
                    'class' => 'md-input'
                ],
                'required' => false
            ))
        ;
    }
}