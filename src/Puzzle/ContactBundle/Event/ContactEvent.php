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
    
    /**
     * @var array
     */
    protected $data;
	
    public function __construct(Contact $contact, array $data = []) {
        $this->contact = $contact;
        $this->data = $data;
	}
	
	public function getContact() {
	    return $this->contact;
	}
	
	public function getData() {
	    return $this->data;
	}
}