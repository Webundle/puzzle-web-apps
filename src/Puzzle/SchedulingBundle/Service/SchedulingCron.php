<?php

namespace Puzzle\SchedulingBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use JMS\JobQueueBundle\Entity\Job;

/**
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 */
class SchedulingCron
{	
	/**
	 * @var Registry $doctrine
	 */
	protected $doctrine;
	
    /**
     * Constructor
     * 
     * @param Registry $doctrine
     */
	public function __construct(Registry $doctrine){
		$this->doctrine = $doctrine;
	}
	
	/**
	 * Add a job
	 *
	 * @param array $params
	 *
	 */
	public function add($params)
	{
		$job = new Job($params['command'], $params['args']);
		
		$em = $this->doctrine->getManager();
		$em->persist($job);
		$em->flush();
		
		return $job->getId();
	}
	
	/**
	 * Add a job depending of another job
	 * Usefull for running a job after another job finishes
	 *
	 * @param array $params
	 *
	 */
	public function addDependingJobs($paramsJob1, $paramsJob2)
	{
	    $em = $this->doctrine->getManager();
		
		$job1 = new Job($paramsJob1['command'], $paramsJob1['args']);
		$job2 = new Job($paramsJob2['command'], $paramsJob2['args']);
		$job2->addDependency($job1);
		
		$em->persist($job1);
		$em->persist($job2);
		$em->flush();
		
		return true;
	}
	
	/**
	 * Add a job relating another job
	 * Usefull for linking a job to another entity
	 * For example to find the job more easily, the job provides a special many-to-any association
	 *
	 * @param array $params
	 *
	 */
	public function addRelatingJob($params, $anyEntity)
	{
		$job = new Job($params['command'], $params['args']);
		$job->addRelatedEntity($anyEntity);
		
		$em = $this->doctrine->getManager();
		$em->persist($job);
		$em->flush();
		
		$em->getRepository('JMSJobQueueBundle:Job')->findJobForRelatedEntity('a', $anyEntity);
		
		return true;
	}
	
	/**
	 * Schedule Job
	 *
	 * @param array $params
	 * @param string $job
	 *
	 */
	public function schedule($params, $jobId = null)
	{
	    $em = $this->doctrine->getManager();
	    
	    if($jobId && $job = $em->getRepository("JMSJobQueueBundle:Job")->find($jobId)){
	    	$em->remove($job);
	    	$em->flush();
	    }
	    
	    $job = new Job($params['command'], $params['args']);
		$job->setExecuteAfter($params['executeAfter']);
		
		$em->persist($job);
		$em->flush();
		
		return $job->getId();
	}
	
	/**
	 * Remove a job
	 *
	 * @param string $id
	 */
	public function remove($id)
	{
	    $em = $this->doctrine->getManager();
	    
	    if($job = $em->getRepository("JMSJobQueueBundle:Job")->find($id)){
	        $em->remove($job);
	        $em->flush();
		}
		
		return true;
	}
}
