<?php

namespace Puzzle\SchedulingBundle\Service;

use Doctrine\ORM\EntityManager;

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
	    $criteria = array(
	        'targetAppName' => $targetAppName,
	        'targetEntityName' => $targetEntityName,
	        'targetEntityId' => $targetEntityId,
	    );
	    
	    $recurrence = $this->em->getRepository("SchedulingBundle:Recurrence")->findOneBy($criteria);
	    
		if ($recurrence !== null) {
		    
		    $now = new \DateTime();
		    $dueAt = $recurrence->getDueAt();
		    
		    if($dueAt == null || $dueAt->getTimestamp() > $now->getTimestamp()){
// 		        $jobId = $this->cron->schedule([
// 		            'executeAfter' => $recurrence->getNextRunAt(),
// 		            'command' => $recurrence->getCommand(),
// 		            'args' => $recurrence->getCommandArgs()
// 		        ], $recurrence->getJob());
		        
// 		        $recurrence->setJob($jobId);
		        
		        $notification = $this->em->getRepository("SchedulingBundle:Notification")->findOneBy($criteria);
		        
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
}
