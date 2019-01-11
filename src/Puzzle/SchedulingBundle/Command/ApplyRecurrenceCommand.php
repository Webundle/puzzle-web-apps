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
class ApplyRecurrenceCommand
{
	/**
	 * @var OutputInterface
	 */
	protected $output;
	
	private $recurrenceId;
	private $intervale;
	
	
	/** 
	 * Configure
	 * 
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure()
	{
		$this->setName('scheduler:recurrence:apply')
			 ->setDescription('Update recurrence')
			 ->addArgument("recurrenceId");
	}
	
	/**
	 * Execute
	 * 
	 * {@inheritDoc}
	 * @see \Symfony\Component\Console\Command\Command::execute()
	 */
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$this->recurrenceId = $input->getArgument("recurrenceId");
		
		$doctrine = $this->getContainer()->get('doctrine');
		$em = $doctrine->getManager('scheduling');
		
		$recurrence = $em->getRepository("SchedulingBundle:Recurrence")->find($this->recurrenceId);
		
		$this->intervale = $recurrence->getIntervale();
		
		$this->getContainer()->get('scheduler')->applyRecurrence($this->recurrenceId);
	}
	
	
	public function shouldBeScheduled(\DateTime $lastRunAt)
	{
		return time() - $lastRunAt->getTimestamp() >= $this->intervale; // Executed at most intervale.
	}
	
	public function createCronJob(\DateTime $lastRunAt)
	{
		return new Job('scheduler:recurrence:apply', array($this->recurrenceId));
	}
	
}