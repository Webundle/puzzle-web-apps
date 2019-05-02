<?php

namespace Puzzle\ExpertiseBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Puzzle\ExpertiseBundle\Entity\Contact;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class ContactEvent extends Event
{

	/**
	 * @var Contact $contact
	 */
	protected $contact;

	/**
	 * @var Response $response
	 */
	protected $response;

	/**
	 * @var array $data
	 */
	protected $data;


	public function __construct(Contact $contact, $data = null) {
	    $this->contact = $contact;
	    $this->data = $data;
	}

	public function getContact() :Contact {
	    return $this->contact;
	}

	public function getResponse() :?Response {
	    return $this->response;
	}

	public function setResponse(Response $response) :self {
	    $this->response = $response;
	    return $this;
	}

	public function getData(){
	    return $this->data;
	}
}

?>
