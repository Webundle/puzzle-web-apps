<?php

namespace Puzzle\SchedulingBundle\Service;

use Doctrine\ORM\EntityManager;
use Puzzle\SchedulingBundle\Entity\Recurrence;
use Puzzle\SchedulingBundle\Entity\Notification;

/**
 * 
 * @author qwincy<qwincypercy@fermentuse.com>
 *
 */
class SchedulingManager
{	
	/**
	 * @var EntityManager $em
	 */
    protected $em;
	
	/**
	 * @var SchedulingCron $cron
	 */
	protected $cron;
	
	/**
	 * @var SchedulingTools $tools
	 */
	protected $tools;
	
	/**
	 * Constructor
	 * 
	 * @param EntityManager $em
	 * @param SchedulingCron $cron
	 * @param SchedulingTools $tools
	 */
	public function __construct(EntityManager $em, SchedulingCron $cron, SchedulingTools $tools) {
	    $this->em = $em;
	    $this->tools = $tools;
	    $this->cron = $cron;
	}
	
	/**
	 * Schedule
	 * 
	 * @param string $targetAppName
	 * @param string $targetEntityName
	 * @param string $targetEntityId
	 */
	public function schedule(string $targetAppName, string $targetEntityName, string $targetEntityId)
	{
	    $criteria = [
	        'targetAppName' => $targetAppName,
	        'targetEntityName' => $targetEntityName,
	        'targetEntityId' => $targetEntityId,
	    ];
	    
	    $recurrence = $this->em->getRepository(Recurrence::class)->findOneBy($criteria);
	    
		if ($recurrence !== null) {
		    $now = new \DateTime();
		    $dueAt = $recurrence->getDueAt();
		    
		    if ($dueAt == null || $dueAt->getTimestamp() > $now->getTimestamp()) {
		        $notification = $this->em->getRepository(Notification::class)->findOneBy($criteria);
		        
		        if ($notification !== null) {
		            $jobId = $this->cron->schedule([
		                'executeAfter' => $notification->getNextRunAt(),
		                'command' => $notification->getCommand(),
		                'args' => $notification->getCommandArgs()
		            ], $notification->getJob());
		            
		            $notification->setJob($jobId);
		            $recurrence->setJob($jobId);
		        }
		    }else{
		        $this->cron->remove($recurrence->getJob());
		        $this->em->remove($recurrence);
		    }
		    
		    $this->em->flush();
		}
		
		return;
	}
	
	public function applyRecurrence($recurrenceId) {
	    /** @var Recurrence **/
	    $recurrence = $this->em->getRepository(Recurrence::class)->find($recurrenceId);
	    
	    $dateNow = new \DateTime();
	    $nextRunAt = $recurrence->getNextRunAt();
	    $intervale = $this->schedulingTools->convertIntervale($recurrence->getIntervale(), $recurrence->getUnity());
	    $repositoryName = ucfirst($recurrence->getTargetAppName()).'Bundle:'.ucfirst($recurrence->getTargetEntityName());
	    
	    if ($nextRunAt->getTimestamp() < $dateNow->getTimestamp()) {
	        $recurrence->setNextRunAt($nextRunAt);
	    }else {
	        $recurrence->setNextRunAt($nextRunAt->add(new \DateInterval("PT".$intervale."M")));
	    }
	    
	    $moment = $this->em->getRepository($repositoryName)->find($recurrence->getEntityId());
	    $moment->setStartedAt($recurrence->getStartedAt()->add($intervale));
	    $moment->setEndedAt($recurrence->getEndedAt()->add($intervale));
	    
	    $this->em->flush();
	    
	    return;
	}
}
