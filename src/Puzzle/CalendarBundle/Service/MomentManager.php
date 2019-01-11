<?php
namespace Puzzle\CalendarBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Puzzle\CalendarBundle\Entity\Moment;
use Puzzle\SchedulingBundle\Service\SchedulingCron;
use Puzzle\SchedulingBundle\Service\SchedulingManager;
use Puzzle\SchedulingBundle\Service\SchedulingTools;
use Puzzle\MediaBundle\Service\UploadManager;
use Puzzle\SchedulingBundle\Util\NotificationUtil;

/**
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 */
class MomentManager
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
	 * @param UploadManager $uploadManager
	 */
	public function __construct(Registry $doctrine, SchedulingManager $schedulingManager, SchedulingCron $schedulingCron, SchedulingTools $schedulingTools)
	{
		$this->doctrine = $doctrine;
		$this->em = $this->doctrine->getManager('calendar');
		$this->schedulingManager = $schedulingManager;
		$this->schedulingCron = $schedulingCron;
		$this->schedulingTools = $schedulingTools;
	}

	/**
	 * Update
	 *
	 * @param string $momentId
	 */
	public function update(string $momentId)
	{
	    $em = $this->doctrine->getManager('scheduling');
	    $moment = $this->em->getRepository("CalendarBundle:Moment")->find($momentId);
	    $recurrence = $em->getRepository("SchedulingBundle:Recurrence")->findOneBy(NotificationUtil::constructTargetCriteria($moment));
	    $notification = $em->getRepository("SchedulingBundle:Notification")->findOneBy(NotificationUtil::constructTargetCriteria($moment));
	    $now = new \DateTime();
	
	    if ($recurrence !== null) {
	        if(! in_array($now->format('w'), $recurrence->getExcludedDays())){
	        	$pastMoment = new Moment();
	        	$pastMoment->setAgenda($moment->getAgenda());
	        	$pastMoment->setColor($moment->getColor());
	        	$pastMoment->setDescription($moment->getDescription());
	        	$pastMoment->setEnableComments($moment->getEnableComments());
	        	$pastMoment->setEndedAt($moment->getEndedAt());
	        	$pastMoment->setLocation($moment->getLocation());
	        	$pastMoment->setPicture($moment->getPicture());
	        	$pastMoment->setStartedAt($moment->getStartedAt());
	        	$pastMoment->setTags($moment->getTags());
	        	$pastMoment->setTitle($moment->getTitle());
	        	$pastMoment->setUser($moment->getUser());
	        	
	        	$this->em->persist($pastMoment);
	        }
	        
	        if ($recurrence->getDueAt() <= $now) {
	            $this->schedulingCron->remove($recurrence->getJob());
	            $em->remove($recurrence);
	            $em->remove($notification);
	        }else {
	            $intervale = $this->schedulingTools->convertIntervale($recurrence->getIntervale(), $recurrence->getUnity());
	            $nextRunAt = $recurrence->getNextRunAt()->add(new \DateInterval("PT".$intervale."M"));
	            
	            if ($nextRunAt->getTimestamp() <= $recurrence->getDueAt()->getTimestamp()) {
	                $recurrence->setNextRunAt($nextRunAt);
	                
	                $startedAt = $moment->getStartedAt();
	                $endedAt = $moment->getEndedAt();
	                $diff = $endedAt->getTimestamp() - $startedAt->getTimestamp();
	                $diff += $nextRunAt->getTimestamp();
	                $date = new \DateTime();
	                $date->setTimestamp($diff);
	                
	                $nextRunAt = $recurrence->getNextRunAt();
	                $moment->setStartedAt($nextRunAt);
	                $moment->setEndedAt($date);
	                
	                $intervale = $this->schedulingTools->convertIntervale($notification->getIntervale(), $notification->getUnity());
	                $nextRunAt = $nextRunAt->sub(new \DateInterval("PT".$intervale."M"));
	                $notification->setNextRunAt($nextRunAt);
	                
	                $this->schedulingManager->schedule("calendar", "moment", $moment->getId());
	            }
	        }
	    }
	    
	    $em->flush();
	    $this->em->flush();
	    
	    return;
	}
}
