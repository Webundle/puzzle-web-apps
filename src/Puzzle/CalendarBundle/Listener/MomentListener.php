<?php

namespace Puzzle\CalendarBundle\Listener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Puzzle\CalendarBundle\Event\MomentEvent;
use Puzzle\SchedulingBundle\Entity\Notification;
use Puzzle\SchedulingBundle\Entity\Recurrence;
use Puzzle\SchedulingBundle\Service\SchedulingCron;
use Puzzle\SchedulingBundle\Service\SchedulingManager;
use Puzzle\SchedulingBundle\Service\SchedulingTools;

/**
 * 
 * @author qwincy
 *
 */
class MomentListener
{
	/**
	 *
	 * @var Registry $doctrine
	 */
	protected $doctrine;
	
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
	public function __construct(Registry $doctrine,SchedulingManager $schedulingManager, SchedulingCron $schedulingCron, SchedulingTools $schedulingTools)
	{
		$this->doctrine = $doctrine;
		$this->em = $this->doctrine->getManager();
		$this->schedulingManager = $schedulingManager;
		$this->schedulingCron = $schedulingCron;
		$this->schedulingTools = $schedulingTools;
	}
	
	public function onSchedule(MomentEvent $event)
	{
		$moment = $event->getMoment();
		$data = $event->getData();
		
		$recurrence = $this->em->getRepository("SchedulingBundle:Recurrence")->findOneBy(array(
				'targetAppName' => "calendar",
				'targetEntityName' => "moment",
				'targetEntityId' => $moment->getId(),
		));
		
		if(! $recurrence){
			$recurrence = new Recurrence();
			$recurrence->setTargetAppName("calendar");
			$recurrence->setTargetEntityName("moment");
			$recurrence->setTargetEntityId($moment->getId());
			
			$this->em->persist($recurrence);
		}
		$dueAt = $data['recurrence_due_at'] ? new \DateTime($data['recurrence_due_at']) : null;
		$nextRunAt = $moment->getEndedAt();
		$intervale = $this->schedulingTools->convertIntervale($data['recurrence_intervale'], $data['recurrence_unity']);
		
// 		$recurrence->setExcludedDays($data['recurrence_excluded_days']);
		$recurrence->setIntervale($data['recurrence_intervale']);
		$recurrence->setUnity($data['recurrence_unity']);
		$recurrence->setDueAt($dueAt);
		$recurrence->setNextRunAt($nextRunAt->add(new \DateInterval("PT".$intervale."M")));
		
		$notification = $this->em->getRepository("SchedulingBundle:Notification")->findOneBy(array(
				'targetAppName' => "calendar",
				'targetEntityName' => "moment",
				'targetEntityId' => $moment->getId()
		));
		
		if(! $notification){
			$notification = new Notification();
			$notification->setTargetAppName("calendar");
			$notification->setTargetEntityName("moment");
			$notification->setTargetEntityId($moment->getId());
			
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
		
		$dateNow = new \DateTime();
		$dueAt = $recurrence->getDueAt();
		
		if(! $recurrence->getJob() || ($dueAt && $dueAt->getTimestamp() < $dateNow->getTimestamp())){
			$this->schedulingManager->schedule("calendar", "moment", $moment->getId());
		}
		
		return true;
	}
}