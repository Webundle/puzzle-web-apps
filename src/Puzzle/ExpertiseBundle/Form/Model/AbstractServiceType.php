<?php

namespace Puzzle\ExpertiseBundle\Form\Model;

use Puzzle\ExpertiseBundle\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.service.name',
            ])
            ->add('description', TextareaType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.service.description',
            ])
            ->add('classIcon', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'expertise.service.classIcon',
                'required' => false
            ])
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'expertise.service.picture',
                'required' => false,
                'mapped' => false
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Service::class,
            'validation_groups' => array(
                Service::class,
                'determineValidationGroups',
            ),
        ));
    }
}