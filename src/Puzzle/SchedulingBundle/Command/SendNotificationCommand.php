<?php
namespace Puzzle\SchedulingBundle\Command;

use JMS\JobQueueBundle\Entity\Job;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 
 * @author qwincy
 *
 */
class SendNotificationCommand
{
	/**
	 * @var OutputInterface
	 */
	protected $output;
	
	private $notificationId;
	
	private $intervale;
	
	
	/** 
	 * Configure
	 * 
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure()
	{
		$this->setName('scheduler:notification:send')
			 ->setDescription('Send notification')
			 ->addArgument("notificationId");
	}
	
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$this->notificationId = $input->getArgument("notificationId");
		$doctrine = $this->getContainer()->get('doctrine');
		$em = $doctrine->getManager('scheduling');
		
		$notification = $em->getRepository("SchedulingBundle:Notification")->find($this->notificationId);
		
		$this->intervale = $notification->getIntervale();
		
		$this->getContainer()->get('scheduler')->sendNotification($this->notificationId);
	}
	
	
	public function shouldBeScheduled(\DateTime $lastRunAt)
	{
		return time() - $lastRunAt->getTimestamp() >= $this->intervale; // Executed at most every intervale.
	}
	
	public function createCronJob(\DateTime $lastRunAt)
	{
		return new Job('scheduler:notification:send', array($this->notificationId));
	}
	
	
}