<?php

namespace Puzzle\MailBundle\Listener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Puzzle\MailBundle\Event\MailEvent;
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
class MailListener
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
	
	/**
	 * Schedule mail sending
	 * 
	 * @param MailEvent $event
	 * @return boolean
	 */
	public function onSchedule(MailEvent $event)
	{
		$mail = $event->getMail();
		$data = $event->getData();
		
		$recurrence = $this->em->getRepository("SchedulingBundle:Recurrence")->findOneBy(array(
				'targetAppName' => "newsletter",
				'targetEntityName' => "mail",
				'targetEntityId' => $mail->getId(),
		));
		
		if ($recurrence === null) {
			$recurrence = new Recurrence();
			$recurrence->setTargetAppName("newsletter");
			$recurrence->setTargetEntityName("mail");
			$recurrence->setTargetEntityId($mail->getId());
			
			$this->em->persist($recurrence);
		}
		
		$dateNow = new \DateTime();
		$dueAt = $data['recurrence_due_at'] ? new \DateTime($data['recurrence_due_at']) : null;
		$nextRunAt = $data['recurrence_next_run_at'] ? new \DateTime($data['recurrence_next_run_at']) : new \DateTime();
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
		
		$notification = $this->em->getRepository("SchedulingBundle:Notification")->findOneBy(array(
				'targetAppName' => "newsletter",
				'targetEntityName' => "mail",
				'targetEntityId' => $mail->getId()
		));
		
		if ($notification === null) {
			$notification = new Notification();
			$notification->setTargetAppName("newsletter");
			$notification->setTargetEntityName("mail");
			$notification->setTargetEntityId($mail->getId());
			
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
			$this->schedulingManager->schedule("newsletter", "mail", $mail->getId());
		}
		
		return true;
	}
	
	/**
	 * Unschedule mail sending
	 *
	 * @param MailEvent $event
	 * @return boolean
	 */
	public function onUnschedule(MailEvent $event)
	{
		$mail = $event->getMail();
		$data = $event->getData();
		
		$recurrence = $this->em->getRepository("SchedulingBundle:Recurrence")->findOneBy(array(
				'targetAppName' => "newsletter",
				'targetEntityName' => "mail",
				'targetEntityId' => $mail->getId(),
		));
		
		if($recurrence){
			if($recurrence->getJob()){
				$this->schedulingCron->remove($recurrence->getJob());
			}
			
			$this->em->remove($recurrence);
		}
		
		$notification = $this->em->getRepository("SchedulingBundle:Notification")->findOneBy(array(
				'targetAppName' => "newsletter",
				'targetEntityName' => "mail",
				'targetEntityId' => $mail->getId()
		));
		
		if($notification){
			if($notification->getJob()){
				$this->schedulingCron->remove($notification->getJob());
			}
			$this->em->remove($notification);
		}
		
		$this->em->flush();
		
		return true;
	}
}