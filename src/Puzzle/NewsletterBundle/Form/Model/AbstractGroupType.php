<?php

namespace Puzzle\NewsletterBundle\Form\Model;

use Puzzle\NewsletterBundle\Entity\Group;
use Puzzle\NewsletterBundle\Entity\Subscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'newsletter.property.group.name'
            ])
            ->add('subscribers', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'newsletter.property.group.subscribers',
                'class' => Subscriber::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Group::class,
            'validation_groups' => array(
                Group::class,
                'determineValidationGroups',
            ),
        ));
    }
}