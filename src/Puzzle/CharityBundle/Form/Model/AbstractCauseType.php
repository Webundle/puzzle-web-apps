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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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
                'label' => 'charity.cause.name'
            ])
            ->add('description', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.cause.description',
                'required' => false
            ))
            ->add('category', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.cause.category',
                'class' => Category::class,
                'choice_label' => 'name',
            ))
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.cause.picture',
                'required' => false,
                'mapped' => false
            ))
            ->add('expiresAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'charity.cause.expiresAt.title',
                'required' => false,
                'mapped' => false
            ])
            ->add('visible', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.cause.visible',
                'required' => false
            ))
            ->add('tags', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.cause.tags',
                'label_attr' => ['class' => 'uk-form-label'],
                'required' => false
            ))
            ->add('totalAmount', IntegerType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.cause.totalAmount',
                'required' => false
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