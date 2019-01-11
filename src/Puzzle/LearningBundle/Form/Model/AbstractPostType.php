<?php

namespace Puzzle\LearningBundle\Form\Model;

use Puzzle\LearningBundle\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\LearningBundle\Entity\Category;

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
                'label' => 'learning.property.post.name',
                'label_attr' => ['class' => 'uk-form-label'],
                'attr' => ['class' => 'md-input'],
            ])
            ->add('description', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.property.post.description',
                'attr' => ['class' => 'md-input'],
                'required' => false
            ))
            ->add('category', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.property.post.category',
                'label_attr' => ['class' => 'uk-form-label'],
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => ['data-md-selectize' => true],
            ))
            ->add('speaker', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'learning.property.post.speaker',
                'label_attr' => ['class' => 'uk-form-label'],
                'attr' => ['class' => 'md-input'],
            ])
            ->add('tags', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.property.post.tags',
                'label_attr' => ['class' => 'uk-form-label'],
                'attr' => ['data-role' => "materialtags"],
                'required' => false
            ))
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.property.post.picture',
                'required' => false,
                'mapped' => false
            ))
            ->add('audio', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.property.post.audio',
                'required' => false,
                'mapped' => false
            ))
            ->add('video', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.property.post.video',
                'required' => false,
                'mapped' => false
            ))
            ->add('document', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.property.post.document',
                'required' => false,
                'mapped' => false
            ))
            ->add('enableComments', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.property.post.enable_comments',
                'attr' => ['data-switchery' => true],
                'required' => false
            ))
            ->add('visible', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.property.post.visible',
                'attr' => ['data-switchery' => true],
                'required' => false
            ))
            ->add('tags', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.property.post.tags',
                'label_attr' => ['class' => 'uk-form-label'],
                'attr' => ['data-role' => "materialtags"],
                'required' => false
            ))
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'messages',
                'label' => 'button.save',
                'attr' => ['class' => "md-fab md-fab-accent"]
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