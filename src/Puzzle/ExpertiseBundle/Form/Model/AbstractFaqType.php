<?php

namespace Puzzle\ExpertiseBundle\Form\Model;

use Puzzle\ExpertiseBundle\Entity\Faq;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractFaqType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('question', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.faq.question',
            ])
            ->add('answer', TextareaType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.faq.answer',
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Faq::class,
            'validation_groups' => array(
                Faq::class,
                'determineValidationGroups',
            ),
        ));
    }
}