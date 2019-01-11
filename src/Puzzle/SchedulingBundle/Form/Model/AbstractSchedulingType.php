<?php

namespace Puzzle\SchedulingBundle\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Puzzle\CalendarBundle\Entity\Agenda;
use Puzzle\SchedulingBundle\Entity\Notification;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractSchedulingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('recurrenceIntervale', NumberType::class, [
                'translation_domain' => 'messages',
                'label' => 'scheduling.property.recurrence.intervale',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
                'mapped' => false,
                'required' => false
            ])
            ->add('recurrenceUnity', ChoiceType::class, array(
                'translation_domain' => 'messages',
                'label' => 'scheduling.property.recurrence.unity.title',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'choices' => array(
                    Notification::UNITY_HOUR => "scheduling.property.recurrence.unity.hour",
                    Notification::UNITY_DAY => "scheduling.property.recurrence.unity.day",
                    Notification::UNITY_WEEK => "scheduling.property.recurrence.unity.week",
                    Notification::UNITY_MONTH => "scheduling.property.recurrence.unity.month",
                    Notification::UNITY_YEAR => "scheduling.property.recurrence.unity.year",
                ),
                'attr' => [
                    'data-md-selectize' => true,
                    'placeholder' => "scheduling.property.recurrence.unity.placeholder"
                ],
                'mapped' => false,
                'required' => false
            ))
            ->add('recurrenceExcludedDays', ChoiceType::class, array(
                'translation_domain' => 'messages',
                'label' => 'scheduling.property.recurrence.excluded_days.title',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'choices' => array(
                    "2" => "scheduling.property.recurrence.excluded_days.monday",
                    "3" => "scheduling.property.recurrence.excluded_days.tuesday",
                    "4" => "scheduling.property.recurrence.excluded_days.wednesday",
                    "5" => "scheduling.property.recurrence.excluded_days.thursday",
                    "6" => "scheduling.property.recurrence.excluded_days.friday",
                    "7" => "scheduling.property.recurrence.excluded_days.saturday",
                    "1" => "scheduling.property.recurrence.excluded_days.sunday"
                ),
                'attr' => [
                    'placeholder' => "scheduling.property.recurrence.excluded_days.placeholder"
                ],
                'mapped' => false,
                'required' => false,
                'multiple' => true
            ))
            ->add('recurrenceDueAt', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'scheduling.property.recurrence.due_at',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [],
                'mapped' => false,
                'required' => false
            ])
        ;
    }
}