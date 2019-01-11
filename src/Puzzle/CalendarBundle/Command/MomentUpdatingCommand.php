<?php
namespace Puzzle\CalendarBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 
 * @author qwincy<qwincypercy@fermentuse.com>
 *
 */
class MomentUpdatingCommand extends ContainerAwareCommand
{
	/** 
	 * Configure
	 * 
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure()
	{
		$this->setName('puzzle:calendar:moment-updating')
			 ->setDescription('Update moment start_at date and ended_at date')
			 ->addArgument("momentId");
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
	    $this->getContainer()->get('calendar.moment_manager')->update($input->getArgument("momentId"));
	}
}