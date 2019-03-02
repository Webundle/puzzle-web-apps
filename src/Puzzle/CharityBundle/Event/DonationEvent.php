<?php 

namespace Puzzle\CharityBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Puzzle\CharityBundle\Entity\Donation;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class DonationEvent extends Event
{
	
	/**
	 * @var Donation $donation
	 */
	protected $donation;
	
	/**
	 * @var Response $response
	 */
	protected $response;
	
	/**
	 * @var array $data
	 */
	protected $data;
	
	
	public function __construct(Donation $donation, $data = null) {
	    $this->donation = $donation;
	    $this->data = $data;
	}
	
	public function getDonation() :Donation {
	    return $this->donation;
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