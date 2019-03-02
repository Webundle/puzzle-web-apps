<?php

namespace Puzzle\UserBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\UserBundle\Form\Model\AbstractUserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class UserCreateType extends AbstractUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder->remove('picture');
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $form = $event->getForm();
            $roles = $options['roles'] ?? [];
            
            $form->add('roles', ChoiceType::class, array(
                'translation_domain' => 'messages',
                'label' => 'user.account.roles',
                'choices' => $roles,
                'multiple' => true,
                'required' => false
            ));
        });
    }
    
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setRequired('roles');
        $resolver->setDefault('csrf_token_id', 'user_create');
        $resolver->setDefault('validation_groups', ['Create']);
    }
    
    public function getBlockPrefix() {
        return 'admin_user_create';
    }
}