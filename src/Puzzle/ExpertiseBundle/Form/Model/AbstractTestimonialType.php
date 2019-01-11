<?php

namespace Puzzle\ExpertiseBundle\Form\Model;

use Puzzle\ExpertiseBundle\Entity\Testimonial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractTestimonialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('author', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.property.testimonial.author',
                'required' => true,
            ])
            ->add('company', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.property.testimonial.company',
                'required' => false,
            ])
            ->add('position', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.property.testimonial.position',
                'required' => false,
            ])
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'expertise.property.testimonial.picture',
                'required' => false,
                'mapped' => false
            ))
            ->add('description', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'expertise.property.testimonial.description',
                'required' => true,
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Testimonial::class,
            'validation_groups' => array(
                Testimonial::class,
                'determineValidationGroups',
            ),
        ));
    }
}