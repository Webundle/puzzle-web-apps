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
                'label' => 'calendar.property.agenda.name'
            ])
            ->add('visibility', ChoiceType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.property.agenda.visibility.name',
                'choices' => array(
                    'private' => "calendar.defaut_value.agenda.visibility.private",
                    'share' => 'calendar.defaut_value.agenda.visibility.share',
                    'public' => 'calendar.defaut_value.agenda.visibility.public',
                ),
            ))
            ->add('memberList', TextareaType::class, array(
                'translation_domain' => 'messages',
                'label' => 'calendar.property.agenda.members',
                'attr' => [
                    'class' => 'uk-width-1-1 md-input autocomplete'
                ],
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