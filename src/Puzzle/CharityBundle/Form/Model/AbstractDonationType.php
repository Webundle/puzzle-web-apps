<?php

namespace Puzzle\CharityBundle\Form\Model;

use Puzzle\CharityBundle\Entity\Donation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\CharityBundle\Entity\Cause;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class AbstractDonationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('author', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'charity.property.donation.author',
                'label_attr' => ['class' => 'uk-form-label'],
                'attr' => ['class' => 'md-input'],
            ])
            ->add('phone', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'charity.property.donation.phone',
                'label_attr' => ['class' => 'uk-form-label'],
                'attr' => ['class' => 'md-input'],
            ])
            ->add('email', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'charity.property.donation.email',
                'label_attr' => ['class' => 'uk-form-label'],
                'attr' => ['class' => 'md-input'],
            ])
            ->add('address', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'charity.property.donation.address',
                'label_attr' => ['class' => 'uk-form-label'],
                'attr' => ['class' => 'md-input'],
            ])
            ->add('cause', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.property.donation.cause',
                'label_attr' => ['class' => 'uk-form-label'],
                'class' => Cause::class,
                'choice_label' => 'name',
                'attr' => ['data-md-selectize' => true],
            ))
            ->add('totalAmount', IntegerType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.property.donation.totalAmount',
                'label_attr' => ['class' => 'uk-form-label'],
                'attr' => ['class' => "md-input"],
                'required' => false
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Donation::class,
            'validation_groups' => array(
                Donation::class,
                'determineValidationGroups',
            ),
        ));
    }
}