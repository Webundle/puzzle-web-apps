<?php

namespace Puzzle\CalendarBundle\Form\Model;

use Puzzle\CalendarBundle\Entity\Moment;
use Puzzle\SchedulingBundle\Form\Model\AbstractSchedulingType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Puzzle\CalendarBundle\Entity\Agenda;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractMomentType extends AbstractSchedulingType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('title', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'calendar.moment.title'
            ])
            ->add('startedAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'calendar.moment.started_at',
                'mapped' => false
            ])
            ->add('endedAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'calendar.moment.ended_at',
                'mapped' => false
            ])
            ->add('location', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'calendar.moment.location',
            ])
            ->add('description', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.moment.description',
                'required' => false,
            ))
            ->add('memberList', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.moment.members',
                'attr' => ['placeholder' => 'calendar.moment.members'],
                'required' => false,
                'mapped' =>false
            ))
            ->add('color', HiddenType::class, [
                'translation_domain' => 'messages',
                'label' => 'calendar.moment.color'
            ])
            ->add('tags', TextType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.moment.tags',
                'required' => false
            ))
            ->add('enableComments', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.moment.enable_comments',
                'required' => false
            ))
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.moment.picture',
                'required' => false
            ))
            ->add('visibility', ChoiceType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.moment.visibility.name',
                'choices' => [
                    'calendar.moment.visibility.private.title' => 'private',
                    'calendar.moment.visibility.share.title' => 'share',
                    'calendar.moment.visibility.public.title' => 'public'
                ]
            ))
            ->add('isRecurrent', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.moment.is_recurrent.title',
                'required' => false
            ))
        ;
            
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $moment = $event->getData();
            $form = $event->getForm();
            
            $user = $options['user'];
            $form->add('agenda', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.moment.agenda',
                'class' => Agenda::class,
                'data'  => $moment->getAgenda(),
                'query_builder' => function (EntityRepository $er)  use ($user) {
                    return $er->createQueryBuilder('a')
                              ->where("a.createdBy = :user")
                              ->setParameter(':user', $user->getFullName())
                              ->orderBy('a.name', 'ASC');
                },
                'choice_label' => 'name',
            ));
        });
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setRequired('user');
        $resolver->setDefaults(array(
            'data_class'        => Moment::class,
            'validation_groups' => array(
                Moment::class,
                'determineValidationGroups',
            ),
        ));
    }
}