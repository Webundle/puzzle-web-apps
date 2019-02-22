<?php

namespace Puzzle\AdvertBundle\Form\Model;

use Puzzle\AdvertBundle\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\AdvertBundle\Entity\Category;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

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
                'label' => 'advert.post.name'
            ])
            ->add('description', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'advert.post.description',
                'required' => false
            ))
            ->add('category', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'advert.post.category',
                'class' => Category::class,
                'choice_label' => 'name',
            ))
            ->add('pinned', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'advert.post.pinned',
                'required' => false
            ))
            ->add('expiresAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'advert.post.expiresAt',
                'mapped' => false,
                'required' => false
            ])
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'blog.post.picture',
                'required' => false,
                'mapped' => false
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