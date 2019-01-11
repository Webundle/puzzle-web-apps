<?php
namespace Puzzle\UserBundle\Twig;

use Doctrine\ORM\EntityManager;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class UserExtension extends \Twig_Extension
{
	/**
	 * @var EntityManager $em
	 */
    protected $em;
	
	public function __construct(EntityManager $em) {
	    $this->em = $em;
	}
	
	public function getFunctions() {
		return [
				new \Twig_SimpleFunction('user_by_id', [$this, 'getUserById'], ['needs_environment' => false, 'is_safe' => ['html']])
		];
	}
	
	public function getUserById($id) {
	    try {
	        return $this->em->getRepository("UserBundle:User")->find($id);
	    } catch (\Exception $e) {
	        return null;
	    }
	}
}
