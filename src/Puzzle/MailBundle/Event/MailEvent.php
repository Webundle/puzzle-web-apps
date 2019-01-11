<?php
namespace Puzzle\MailBundle\Event;
use Symfony\Component\EventDispatcher\Event;
use Puzzle\MailBundle\Entity\Mail;

class MailEvent extends Event
{
	protected $mail;
	protected $data;
	
	public function __construct(Mail $mail, array $data = null)
	{
		$this->mail = $mail;
		$this->data = $data;
	}
	
	public function getMail()
	{
		return $this->mail;
	}
	
	public function getData()
	{
		return $this->data;
	}
}