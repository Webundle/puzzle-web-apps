<?php

namespace Puzzle\NewsletterBundle\Form\Model;

use Puzzle\NewsletterBundle\Entity\Subscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class AbstractSubscriberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'newsletter.property.subscriber.name',
                'required' => false
            ])
            ->add('email', EmailType::class, array(
                'translation_domain' => 'messages',
                'label' => 'newsletter.property.subscriber.email',
                'required' => true
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Subscriber::class,
            'validation_groups' => array(
                Subscriber::class,
                'determineValidationGroups',
            ),
        ));
    }
}