<?php

namespace Puzzle\StaticBundle\Form\Model;

use Puzzle\StaticBundle\Entity\Page;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\StaticBundle\Entity\Template;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractPageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'static.page.name'
            ])
            ->add('template', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'static.page.template',
                'class' => Template::class,
                'choice_label' => 'name',
                'required' => false
            ))
            ->add('content', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'static.page.content'
            ))
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'static.page.picture',
                'required' => false
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Page::class,
            'validation_groups' => array(
                Page::class,
                'determineValidationGroups',
            ),
        ));
    }
}