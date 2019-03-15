<?php

namespace Puzzle\CurriculumBundle\Form\Model;

use Puzzle\CurriculumBundle\Entity\Work;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class AbstractWorkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.work.name'
            ])
            ->add('position', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.work.position'
            ])
            ->add('description', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'curriculum.work.description',
                'required' => false,
            ))
            ->add('startedAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.work.startedAt',
                'mapped' => false
            ])
            ->add('endedAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.work.endedAt',
                'mapped' => false
            ])
            ->add('company', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.work.company',
            ])
            ->add('address', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.work.address',
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Work::class,
            'validation_groups' => array(
                Work::class,
                'determineValidationGroups',
            ),
        ));
    }
}