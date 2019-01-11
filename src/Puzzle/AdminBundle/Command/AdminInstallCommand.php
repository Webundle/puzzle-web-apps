<?php

namespace Puzzle\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Puzzle\AdminBundle\AdminEvents;
use Puzzle\AdminBundle\Event\AdminInstallationEvent;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class AdminInstallCommand extends ContainerAwareCommand
{
	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure() {
		$this->setName('puzzle:admin-install');
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Console\Command\Command::execute()
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
	    /** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */
	    $dispatcher = $this->getContainer()->get('event_dispatcher');
	    
	    try {
	        $dispatcher->dispatch(AdminEvents::ADMIN_INSTALLING, new AdminInstallationEvent($output));
	    } catch (\Exception $e) {
	        $dispatcher->dispatch(AdminEvents::ADMIN_INSTALLATION_ROLLBACK, new AdminInstallationEvent($output));
	        $output->writeln('<fg=black;bg=red>[Error] Admin installation has failed.</>');
	        $output->writeln($e->getMessage());
	        exit();
	    }
	    
	    $dispatcher->dispatch(AdminEvents::ADMIN_INSTALLED, new AdminInstallationEvent($output));
	    $output->writeln(['', '<fg=black;bg=green>[OK] Admin is successfully installed.</>']);
	}
}