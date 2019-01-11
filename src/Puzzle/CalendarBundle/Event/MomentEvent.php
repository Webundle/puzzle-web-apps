<?php
namespace Puzzle\CalendarBundle\Event;
use Symfony\Component\EventDispatcher\Event;
use Puzzle\CalendarBundle\Entity\Moment;

class MomentEvent extends Event
{
	protected $moment;
	protected $data;
	
	public function __construct(Moment $moment, array $data = null){
		$this->moment = $moment;
		$this->data = $data;
	}
	
	public function getMoment(){
		return $this->moment;
	}
	
	public function getData(){
		return $this->data;
	}
}