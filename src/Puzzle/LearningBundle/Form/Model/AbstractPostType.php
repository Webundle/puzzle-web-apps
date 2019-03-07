<?php

namespace Puzzle\LearningBundle\Form\Model;

use Puzzle\LearningBundle\Entity\Category;
use Puzzle\LearningBundle\Entity\Post;
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
                'label' => 'learning.post.name'
            ])
            ->add('description', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.post.description',
                'required' => false
            ))
            ->add('category', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.post.category',
                'class' => Category::class,
                'choice_label' => 'name'
            ))
            ->add('speaker', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'learning.post.speaker',
            ])
            ->add('tags', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.post.tags',
                'required' => false
            ))
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.post.picture',
                'required' => false,
                'mapped' => false
            ))
            ->add('audio', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.post.audio',
                'required' => false,
                'mapped' => false
            ))
            ->add('video', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.post.video',
                'required' => false,
                'mapped' => false
            ))
            ->add('document', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.post.document',
                'required' => false,
                'mapped' => false
            ))
            ->add('enableComments', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.post.enable_comments',
                'required' => false
            ))
            ->add('visible', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.post.visible',
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