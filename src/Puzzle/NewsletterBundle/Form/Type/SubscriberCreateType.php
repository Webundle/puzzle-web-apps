<?php

namespace Puzzle\NewsletterBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\NewsletterBundle\Form\Model\AbstractSubscriberType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class SubscriberCreateType extends AbstractSubscriberType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'newsletter_subscriber_create');
        $resolver->setDefault('validation_subscribers', ['Create']);
    }
    
    public function getBlockPrefix() {
        return 'admin_newsletter_subscriber_create';
    }
}