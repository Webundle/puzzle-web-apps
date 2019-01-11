<?php
namespace Puzzle\AdminBundle\Event;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class AdminInstallationEvent extends Event {
	/**
	 * @var OutputInterface $output
	 */
	private $output;
	
	public function __construct(OutputInterface $output) {
		$this->output = $output;
	}
	
	public function getOuput() :OutputInterface {
		return $this->output;
	}
	
	public function notifySuccess(string $message) {
		$this->output->writeln(sprintf('<comment>%s</> <info>[success]</> %s', date('H:i:s'), $message));
	}
	
	public function notifyError(string $message) {
		$this->output->writeln(sprintf('<comment>%s</> <fg=red>[error]</> %s', date('H:i:s'), $message));
	}
}
