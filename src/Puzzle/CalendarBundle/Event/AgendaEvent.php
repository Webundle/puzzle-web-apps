<?php
namespace Puzzle\CalendarBundle\Event;
use Symfony\Component\EventDispatcher\Event;
use Puzzle\CalendarBundle\Entity\Agenda;

class AgendaEvent extends Event
{
	protected $agenda;
	protected $data;
	
	public function __construct(Agenda $agenda, array $data = null){
		$this->agenda = $agenda;
		$this->data = $data;
	}
	
	public function getAgenda(){
		return $this->agenda;
	}
	
	public function getData(){
		return $this->data;
	}
}