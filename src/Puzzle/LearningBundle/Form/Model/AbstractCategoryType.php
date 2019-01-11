<?php

namespace Puzzle\LearningBundle\Form\Model;

use Puzzle\LearningBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'learning.property.post.name',
                'label_attr' => ['class' => 'uk-form-label'],
                'attr' => ['class' => 'md-input slugglable'],
            ])
            ->add('description', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.property.post.description',
                'attr' => ['class' => 'md-input'],
                'required' => false
            ))
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'learning.property.category.picture',
                'required' => false,
                'mapped' => false
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
            'data_class' => Category::class,
            'validation_groups' => array(
                Category::class,
                'determineValidationGroups',
            ),
        ));
    }
}