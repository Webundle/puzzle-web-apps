<?php

namespace Puzzle\AdminBundle\Form\Model;

use Puzzle\AdminBundle\Entity\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractMenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'admin.property.menu.name',
                'label_attr' => ['class' => 'uk-form-label'],
                'attr' => ['class' => 'md-input'],
            ])
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'messages',
                'label' => 'button.save',
                'attr' => ['class' => "md-btn md-btn-success"]
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Menu::class,
            'validation_groups' => array(
                Menu::class,
                'determineValidationGroups',
            ),
        ));
    }
}