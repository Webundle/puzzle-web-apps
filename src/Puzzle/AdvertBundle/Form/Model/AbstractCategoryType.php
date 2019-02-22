<?php

namespace Puzzle\AdvertBundle\Form\Model;

use Puzzle\AdvertBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'advert.category.name'
            ])
            ->add('description', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'advert.post.description',
                'required' => false
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Category::class,
            'validation_groups' => array(
                Category::class,
                'determineValidationGroups',
            ),
        ));
    }
}