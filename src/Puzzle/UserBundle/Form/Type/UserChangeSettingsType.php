<?php
namespace Puzzle\UserBundle\Form\Type;

use Puzzle\UserBundle\Form\Model\AbstractUserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class UserChangeSettingsType extends AbstractUserType
{
	public function __construct() {
		parent::__construct(false);
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		parent::buildForm($builder, $options);
		
		$builder->remove('roles')
// 				->remove('username')
				->remove('plainPassword')
				->remove('accountExpiresAt')
				->remove('credentialsExpiresAt');
	}
	
	public function configureOptions(OptionsResolver $resolver) {
		parent::configureOptions($resolver);
		
		$resolver->setDefault('csrf_token_id', 'user_settings');
		$resolver->setDefault('validation_groups', ['Update']);
	}
	
	public function getBlockPrefix() {
		return 'app_user_settings';
	}
}
