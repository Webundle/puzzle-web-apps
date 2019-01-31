<?php

namespace Puzzle\ExpertiseBundle\Form\Model;

use Puzzle\ExpertiseBundle\Entity\Pricing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractPricingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.pricing.name',
            ])
            ->add('description', TextareaType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.pricing.description',
            ])
            ->add('amount', IntegerType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.pricing.amount',
                'required' => false
            ])
            ->add('currency', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.pricing.currency',
                'required' => false
            ])
            ->add('period', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.pricing.period',
                'required' => false
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Pricing::class,
            'validation_groups' => array(
                Pricing::class,
                'determineValidationGroups',
            ),
        ));
    }
}