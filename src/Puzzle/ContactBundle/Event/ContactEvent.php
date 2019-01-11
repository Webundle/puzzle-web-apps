<?php
namespace Puzzle\ContactBundle\Event;
use Symfony\Component\EventDispatcher\Event;
use Puzzle\ContactBundle\Entity\Contact;

class ContactEvent extends Event
{
    /**
     * @var Contact
     */
    protected $contact;
	
    public function __construct(Contact $contact) {
        $this->contact = $contact;
	}
	
	public function getContact() {
	    return $this->contact;
	}
}