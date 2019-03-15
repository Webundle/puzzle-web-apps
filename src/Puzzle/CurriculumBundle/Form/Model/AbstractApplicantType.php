<?php

namespace Puzzle\CurriculumBundle\Form\Model;

use Puzzle\CurriculumBundle\Entity\Applicant;
use Puzzle\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class AbstractApplicantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('firstName', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.applicant.firstName'
            ])
            ->add('lastName', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.applicant.lastName'
            ])
            ->add('birthday', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.applicant.birthday',
                'mapped' => false
            ])
            ->add('phoneNumber', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.applicant.phoneNumber',
                'required' => false
            ])
            ->add('email', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.applicant.email'
            ])
            ->add('biography', TextareaType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.applicant.biography',
                'required' => false
            ])
            ->add('website', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.applicant.website',
                'required' => false
            ])
            ->add('address', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.applicant.address',
                'required' => false
            ])
            ->add('user', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'curriculum.applicant.user',
                'class' => User::class,
                'required' => false,
                'choice_label' => 'fullName'
            ))
            ->add('childCount', IntegerType::class, [
                'translation_domain' => 'messages',
                'label' => 'curriculum.applicant.childCount',
                'required' => false
            ])
            ->add('single', ChoiceType::class, array(
                'translation_domain' => 'messages',
                'label' => 'curriculum.applicant.single.title',
                'choices' => array(
                    "curriculum.applicant.single.not_married" => '0',
                    "curriculum.applicant.single.married" => '1'
                ),
            ))
            ->add('skills', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'curriculum.applicant.skills',
                'required' => false
            ))
            ->add('hobbies', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'curriculum.applicant.hobbies',
                'required' => false
            ))
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'curriculum.applicant.picture',
                'required' => false,
                'mapped' => false
            ))
            ->add('file', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'curriculum.applicant.file',
                'required' => false,
                'mapped' => false
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Applicant::class,
            'validation_groups' => array(
                Applicant::class,
                'determineValidationGroups',
            ),
        ));
    }
}