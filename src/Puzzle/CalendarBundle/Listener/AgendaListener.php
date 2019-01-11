<?php

namespace Puzzle\CalendarBundle\Listener;

use Doctrine\ORM\EntityManager;
use Puzzle\CalendarBundle\Event\AgendaEvent;

/**
 * 
 * @author AGNES Gnagne Cedric<cecenho55@gmail.com>
 *
 */
class AgendaListener
{
	/**
	 *
	 * @var EntityManager $em
	 */
	protected $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	public function onShare(AgendaEvent $event)
	{
		$agenda = $event->getAgenda();
		$data = $event->getData();
		
		$memberProviderSelector = $data['memberProvider'];
		$ids = $data[$memberProviderSelector];
		
		$usersId = [];
		
		if ($memberProviderSelector === "groups") {
		    foreach ($ids as $id) {
		        $group = $this->em->getRepository("UserBundle:Group")->find($id);
		        $usersId = array_merge($usersId, $group->getUsers());
		    }
		}else {
		    $usersId = explode(',', $ids);
		}
		
		foreach ($usersId as $id) {
		    $member = $this->em->getRepository('UserBundle:User')->find($id);
		    $agenda->addMember($member);
		}
		
		return $this->em->flush();
	}
	
}