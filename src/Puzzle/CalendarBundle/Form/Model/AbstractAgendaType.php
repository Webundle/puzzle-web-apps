<?php

namespace Puzzle\CalendarBundle\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Puzzle\CalendarBundle\Entity\Agenda;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractAgendaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'calendar.agenda.name'
            ])
            ->add('visibility', ChoiceType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.agenda.visibility.name',
                'choices' => array(
                    "calendar.agenda.visibility.private.title" => 'private',
                    "calendar.agenda.visibility.share.title" => 'share',
                    "calendar.agenda.visibility.public.title" => 'public',
                ),
            ))
            ->add('memberList', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.agenda.members',
                'required' => false,
                'mapped' =>false
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Agenda::class,
            'validation_groups' => array(
                Agenda::class,
                'determineValidationGroups',
            ),
        ));
    }
}