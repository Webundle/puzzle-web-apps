<?php
namespace Puzzle\NewsletterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class MailBroadcastingCommand extends ContainerAwareCommand
{
    private $subject;
    private $template;
    
	/** 
	 * Configure
	 * 
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure()
	{
		$this->setName('puzzle:newsletter:subscriber-mail-broadcasting')
			 ->setDescription('Send mail to all subscribers');
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Console\Command\Command::interact()
	 */
	protected function interact(InputInterface $input, OutputInterface $output)
	{
	    $dialog = $this->getHelper('question');
	    
	    $this->subject = $dialog->ask($input, $output, new Question('Sujet: '));
	    $this->template = $dialog->ask($input, $output, new Question('Modèle: '));
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
	    $this->getContainer()->get('newsletter.mail_sender')->sendBroadcast($this->subject, $this->template);
	}
}