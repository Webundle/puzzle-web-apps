<?php 

namespace Puzzle\MailBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Puzzle\MailBundle\Entity\Contact;

class ContactEvent extends Event
{
	/**
	 * 
	 * @var Contact
	 */
	private $contact;
	
	public function __construct(Contact $contact)
	{
		$this->contact= $contact;
	}
	
	public function getRequest()
	{
		return $this->contact;
	}
	
}

?>