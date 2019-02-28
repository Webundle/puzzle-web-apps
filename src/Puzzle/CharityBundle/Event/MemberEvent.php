<?php 

namespace Puzzle\CharityBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Puzzle\CharityBundle\Entity\Member;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class MemberEvent extends Event
{
	
	/**
	 * @var Member $member
	 */
	protected $member;
	
	/**
	 * @var Response $response
	 */
	protected $response;
	
	/**
	 * @var array $data
	 */
	protected $data;
	
	
	public function __construct(Member $member, $data = null) {
	    $this->member = $member;
	    $this->data = $data;
	}
	
	public function getMember() :Member {
	    return $this->member;
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