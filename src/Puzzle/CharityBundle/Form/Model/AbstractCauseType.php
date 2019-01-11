<?php

namespace Puzzle\CharityBundle\Form\Model;

use Puzzle\CharityBundle\Entity\Cause;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\CharityBundle\Entity\Category;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class AbstractCauseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'charity.property.cause.name',
                'label_attr' => ['class' => 'uk-form-label'],
                'attr' => ['class' => 'md-input'],
            ])
            ->add('description', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.property.cause.description',
                'attr' => ['class' => 'md-input'],
                'required' => false
            ))
            ->add('category', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.property.cause.category',
                'label_attr' => ['class' => 'uk-form-label'],
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => ['data-md-selectize' => true],
            ))
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.property.cause.picture',
                'required' => false,
                'mapped' => false
            ))
            ->add('startedAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.property.project.startedAt',
                'label_attr' => ['class' => 'uk-form-label'],
                'required' => false,
                'mapped' => false
            ])
            ->add('endedAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.property.project.endedAt',
                'label_attr' => ['class' => 'uk-form-label'],
                'required' => false,
                'mapped' => false
            ])
            ->add('tags', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.property.cause.tags',
                'label_attr' => ['class' => 'uk-form-label'],
                'attr' => ['data-role' => "materialtags"],
                'required' => false
            ))
            ->add('totalAmount', IntegerType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.property.cause.totalAmount',
                'label_attr' => ['class' => 'uk-form-label'],
                'attr' => ['class' => "md-input"],
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
            'data_class' => Cause::class,
            'validation_groups' => array(
                Cause::class,
                'determineValidationGroups',
            ),
        ));
    }
}