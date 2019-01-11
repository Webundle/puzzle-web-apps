<?php
namespace Puzzle\SchedulingBundle\Event;
use Symfony\Component\EventDispatcher\Event;

class SchedulingEvent extends Event
{
	protected $data;
	
	public function __construct(array $data) {
		$this->data = $data;
	}
	
	public function getData() {
		return $this->data;
	}
}