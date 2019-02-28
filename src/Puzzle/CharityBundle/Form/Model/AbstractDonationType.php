<?php

namespace Puzzle\CharityBundle\Form\Model;

use Puzzle\CharityBundle\Entity\Donation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\CharityBundle\Entity\Cause;
use Puzzle\CharityBundle\Entity\Member;

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
            ->add('cause', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.donation.cause',
                'class' => Cause::class,
                'choice_label' => 'name'
            ))
            ->add('member', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.donation.member',
                'class' => Member::class,
                'choice_label' => 'firstName'
            ))
            ->add('totalAmount', IntegerType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.donation.totalAmount',
                'required' => false
            ))
            ->add('paidAmount', IntegerType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.donation.paidAmount',
                'required' => false
            ))
            ->add('countDonationLines', IntegerType::class, array(
                'translation_domain' => 'messages',
                'label' => 'charity.donation.countDonationLines',
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