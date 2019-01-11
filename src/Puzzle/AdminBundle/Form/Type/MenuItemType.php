<?php

namespace Puzzle\AdminBundle\Form\Type;

use Puzzle\AdminBundle\Entity\MenuItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class MenuItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'admin.property.menuItem.name',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input',
                    'title' => ""
                ],
            ])
            ->add('page', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'admin.property.menuItem.page',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'class' => 'StaticBundle:Page',
                'choice_label' => 'name',
                'attr' => [
//                     'data-md-selectize' => true,
                    'class' => 'md-input'
                ],
                'required'   => false,
                'placeholder' => 'Pas de page associée',
            ))
            ->add('position', IntegerType::class, [
                'translation_domain' => 'messages',
                'label' => 'admin.property.menuItem.position',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input',
                    'title' => ""
                ],
            ])
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'messages',
                'label' => 'button.save',
                'attr' => [
                    'class' => "md-btn md-btn-success"
                ]
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => MenuItem::class,
            'validation_groups' => array(
                MenuItem::class,
                'determineValidationGroups',
            ),
        ));
    }
}