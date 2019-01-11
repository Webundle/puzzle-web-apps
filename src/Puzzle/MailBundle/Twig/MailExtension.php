<?php
namespace Puzzle\NewsletterBundle\Twig;

use Doctrine\ORM\EntityManager;

/**
 *
 * @author Baidai Cedrick Oka <cedric.baidai@veone.net>
 *
 */
class MailExtension extends \Twig_Extension
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
		    new \Twig_SimpleFunction('count_groups', [$this, 'countGroups'], ['needs_environment' => false, 'is_safe' => ['html']]),
			new \Twig_SimpleFunction('count_subscribers_group', [$this, 'countSubscribersByGroupName'], ['needs_environment' => false, 'is_safe' => ['html']]),
		    new \Twig_SimpleFunction('count_subscribers_group_reverse', [$this, 'countSubscribersByNotGroupName'], ['needs_environment' => false, 'is_safe' => ['html']])
		];
	}
	
	public function countGroups(array $criteria) {
	    return $this->em->getRepository("NewsletterBundle:Group")->countBy(
	        null, null, $criteria
	    );
	}
	
	/**
	 * Count subscribers by group name
	 * 
	 * @param string $groupName
	 * @return int
	 */
	public function countSubscribersByGroupName(string $groupName) {
	    $group = $this->em->getRepository("NewsletterBundle:Group")->findOneBy(['name' => $groupName]);
	    
	    if ($group === null) {
	        return null;
	    }
	    
	    $subscribers = $group->getSubscribers();
	    $list = null;
	    
	    if ($subscribers !== null) {
	        foreach ($subscribers as $key => $subscriber){
	            $list = $key <= 0 ? "'".$subscriber."'": $list.','."'".$subscriber."'";
	        }
	    }
	    
	    return $this->em->getRepository("NewsletterBundle:Subscriber")->countBy(
	        null, [['id', null, 'IN ('.$list.')']]
	    );
	}
	
	/**
	 * Count subscribers by not group name
	 * @param string $groupName
	 * @return int
	 */
	public function countSubscribersByNotGroupName(string $groupName) {
	    $groups = $this->em->getRepository("NewsletterBundle:Group")->customFindBy(
	        null, null, [['name', $groupName, '!=']]
	    );
	    
	    $subscribers = [];
	    foreach ($groups as $promotion) {
	        if ($promotion->getSubscribers() !== null) {
	            $subscribers = array_merge($subscribers, $promotion->getSubscribers());
	        }
	    }
	    
	    $subscribers = array_unique($subscribers);
	    $list = null;
	    
	    if ($subscribers !== null) {
	        foreach ($subscribers as $key => $subscriber){
	            $list = $key <= 0 ? "'".$subscriber."'": $list.','."'".$subscriber."'";
	        }
	    }
	    
	    return $this->em->getRepository("NewsletterBundle:Subscriber")->countBy(
	        null, [['id', null, 'IN ('.$list.')']]
	    );
	}
}
