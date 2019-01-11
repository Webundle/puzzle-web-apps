<?php

namespace Puzzle\CalendarBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Puzzle\CalendarBundle\Entity\Moment;
use Doctrine\ORM\EntityRepository;
use Puzzle\SchedulingBundle\Form\Model\AbstractSchedulingType;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class MomentFormType extends AbstractSchedulingType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('title', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'calendar.property.moment.title',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input slugglable'
                ],
            ])
            ->add('slug', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'calendar.property.moment.slug',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input slug'
                ],
            ])
            ->add('startedAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'calendar.property.moment.started_at',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [],
                'mapped' => false
            ])
            ->add('endedAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'calendar.property.moment.ended_at',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [],
                'mapped' => false
            ])
            ->add('location', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'calendar.property.moment.location',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
            ])
            ->add('description', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'blog.label.post.description',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'required' => false,
                'attr' => [
                    'class' => 'md-input'
                ],
            ))
            ->add('memberList', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.property.moment.members',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'uk-width-1-1 md-input autocomplete'
                ],
                'required' => false,
                'mapped' =>false
            ))
            ->add('color', HiddenType::class, [
                'translation_domain' => 'messages',
                'label' => 'calendar.property.moment.color',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
            ])
            ->add('tags', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.property.moment.tags',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'data-role' => "materialtags"
                ],
                'required' => false
            ))
            ->add('enableComments', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.property.moment.enable_comments',
                'attr' => [
                    'data-switchery' => true
                ],
                'label_attr' => [
                    'class' => 'uk-display-block uk-margin-small-top uk-form-label'
                ],
                'required' => false
            ))
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.property.moment.picture',
                'required' => false
            ))
            ->add('visibility', ChoiceType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.property.moment.visibility.name',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'choices' => array(
                    'private' => "calendar.property.moment.visibility.private",
                    'share' => 'calendar.property.moment.visibility.share',
                    'public' => 'calendar.property.moment.visibility.public',
                ),
                'attr' => [
                    'data-md-selectize' => true
                ],
            ))
            ->add('isRecurrent', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.property.moment.is_recurrent',
                'label_attr' => [
                    'class' => 'uk-display-block uk-margin-small-top uk-form-label'
                ],
                'attr' => [
                    'data-switchery' => true
                ],
                'required' => false
            ))
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'messages',
                'label' => 'button.label.save',
                'attr' => [
                    'class' => "md-fab md-fab-accent"
                ]
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Moment::class,
            'validation_groups' => array(
                Moment::class,
                'determineValidationGroups',
            ),
        ));
    }
}