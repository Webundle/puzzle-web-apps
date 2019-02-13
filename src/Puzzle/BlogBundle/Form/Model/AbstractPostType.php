<?php

namespace Puzzle\BlogBundle\Form\Model;

use Puzzle\BlogBundle\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class AbstractPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'blog.post.name'
            ])
            ->add('description', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'blog.post.description',
                'required' => false
            ))
            ->add('category', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'blog.post.category',
                'class' => 'BlogBundle:Category',
                'choice_label' => 'name',
            ))
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'blog.post.picture',
                'required' => false,
                'mapped' => false
            ))
            ->add('pictures', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'blog.post.pictures',
                'required' => false,
                'mapped' => false
            ))
            ->add('enableComments', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'blog.post.enable_comments',
                'required' => false
            ))
            ->add('visible', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'blog.post.visible',
                'required' => false
            ))
            ->add('flash', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'blog.post.isFlash',
                'required' => false
            ))
            ->add('flashExpiresAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'blog.post.flashExpiresAt',
                'mapped' => false,
                'required' => false
            ])
            ->add('tags', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'blog.post.tags',
                'required' => false
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Post::class,
            'validation_groups' => array(
                Post::class,
                'determineValidationGroups',
            ),
        ));
    }
}