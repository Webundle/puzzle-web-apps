<?php
namespace Puzzle\NewsletterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 
 * @author qwincy<qwincypercy@fermentuse.com>
 *
 */
class MailSchedulingCommand extends ContainerAwareCommand
{
	/** 
	 * Configure
	 * 
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure()
	{
		$this->setName('puzzle:newsletter:schedule-mail-sending')
			 ->setDescription('Schedule mail sending')
			 ->addArgument("mailId");
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
		$this->getContainer()->get('newsletter.mail_manager')->apply($input->getArgument("mailId"));
	}
}