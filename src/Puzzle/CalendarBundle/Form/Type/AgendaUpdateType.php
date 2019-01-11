<?php

namespace Puzzle\CalendarBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\CalendarBundle\Form\Model\AbstractAgendaType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class AgendaUpdateType extends AbstractAgendaType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'agenda_update');
        $resolver->setDefault('validation_groups', ['Update']);
    }
    
    public function getBlockPrefix() {
        return 'admin_calendar_agenda_update';
    }
}