<?php

namespace Puzzle\SchedulingBundle\Listener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Puzzle\SchedulingBundle\Entity\Notification;
use Puzzle\SchedulingBundle\Entity\Recurrence;
use Puzzle\SchedulingBundle\Service\SchedulingCron;
use Puzzle\SchedulingBundle\Service\SchedulingManager;
use Puzzle\SchedulingBundle\Service\SchedulingTools;
use Puzzle\SchedulingBundle\Event\SchedulingEvent;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author qwincy
 *
 */
class SchedulingListener
{	
	/**
	 *
	 * @var EntityManager $em
	 */
	protected $em;
	
	/**
	 *
	 * @var SchedulingManager $schedulingManager
	 */
	protected $schedulingManager;
	
	/**
	 *
	 * @var SchedulingCron $schedulingCron
	 */
	protected $schedulingCron;
	
	/**
	 *
	 * @var SchedulingTools $schedulingTools
	 */
	protected $schedulingTools;
	
	/**
	 * Constructor
	 *
	 * @param Registry $doctrine
	 * @param SchedulingManager $schedulingManager
	 * @param SchedulingTools $schedulingTools
	 */
	public function __construct(EntityManager $em, SchedulingManager $schedulingManager, SchedulingCron $schedulingCron, SchedulingTools $schedulingTools)
	{
		$this->em = $em;
		$this->schedulingManager = $schedulingManager;
		$this->schedulingCron = $schedulingCron;
		$this->schedulingTools = $schedulingTools;
	}
	
	/**
	 * Schedule
	 * 
	 * @param SchedulingEvent $event
	 * @return boolean
	 */
	public function onSchedule(SchedulingEvent $event)
	{
		$data = $event->getData();
		$criteria = [
		    'targetAppName' => $data['targetAppName'],
		    'targetEntityName' => $data['targetEntityName'],
		    'targetEntityId' => $data['targetEntityId'],
		];
		
		$recurrence = $this->em->getRepository("SchedulingBundle:Recurrence")->findOneBy($criteria);
		
		if ($recurrence === null) {
			$recurrence = new Recurrence();
			$recurrence->setTargetAppName($data['targetAppName']);
			$recurrence->setTargetEntityName($data['targetEntityName']);
			$recurrence->setTargetEntityId($data['targetEntityId']);
			
			$this->em->persist($recurrence);
		}
		
		$dateNow = new \DateTime();
		$dueAt = isset($data['recurrence_due_at']) ? new \DateTime($data['recurrence_due_at']) : null;
		$nextRunAt = isset($data['recurrence_next_run_at']) ? new \DateTime($data['recurrence_next_run_at']) : new \DateTime();
		$intervale = $this->schedulingTools->convertIntervale($data['recurrence_intervale'], $data['recurrence_unity']);
		
		$recurrence->setExcludedDays($data['recurrence_excluded_days']);
		$recurrence->setIntervale($data['recurrence_intervale']);
		$recurrence->setUnity($data['recurrence_unity']);
		$recurrence->setDueAt($dueAt);
		
		if ($nextRunAt->getTimestamp() < $dateNow->getTimestamp()) {
			$recurrence->setNextRunAt($nextRunAt);
		}else {
			$recurrence->setNextRunAt($nextRunAt->add(new \DateInterval("PT".$intervale."M")));
		}
		
		$notification = $this->em->getRepository("SchedulingBundle:Notification")->findOneBy($criteria);
		
		if ($notification === null) {
			$notification = new Notification();
			$notification->setTargetAppName($data['targetAppName']);
			$notification->setTargetEntityName($data['targetEntityName']);
			$notification->setTargetEntityId($data['targetEntityId']);
			
			$this->em->persist($notification);
		}
		
		$notification->setChannel($data['notification_channel']);
		$notification->setIntervale($data['notification_intervale']);
		$notification->setUnity($data['notification_unity']);
		$notification->setCommand($data['notification_command']);
		$notification->setCommandArgs($data['notification_command_args']);
		
		$nextRunAt = $recurrence->getNextRunAt();
		$intervale = $this->schedulingTools->convertIntervale($notification->getIntervale(), $notification->getUnity());
		$notification->setNextRunAt($nextRunAt->add(new \DateInterval("PT".$intervale."M")));
		
		$this->em->flush();
		
		$dueAt = $recurrence->getDueAt();
		
		if (! $recurrence->getJob() || ($dueAt && $dueAt->getTimestamp() < $dateNow->getTimestamp())) {
		    $this->schedulingManager->schedule($data['targetAppName'], $data['targetEntityName'], $data['targetEntityId']);
		}
		
		return true;
	}
	
	/**
	 * Unschedule
	 *
	 * @param SchedulingEvent $event
	 * @return boolean
	 */
	public function onUnschedule(SchedulingEvent $event) {
		$data = $event->getData();
		$criteria = [
		    'targetAppName' => $data['targetAppName'],
		    'targetEntityName' => $data['targetEntityName'],
		    'targetEntityId' => $data['targetEntityId'],
		];
		
		$recurrence = $this->em->getRepository("SchedulingBundle:Recurrence")->findOneBy($criteria);
		
		if ($recurrence !== null) {
			if ($recurrence->getJob()) {
				$this->schedulingCron->remove($recurrence->getJob());
			}
			
			$this->em->remove($recurrence);
		}
		
		$notification = $this->em->getRepository("SchedulingBundle:Notification")->findOneBy($criteria);
		
		if ($notification !== null) {
			if ($notification->getJob()) {
				$this->schedulingCron->remove($notification->getJob());
			}
			
			$this->em->remove($notification);
		}
		
		$this->em->flush();
		
		return true;
	}
}