<?php
namespace Puzzle\NewsletterBundle\Event;
use Symfony\Component\EventDispatcher\Event;
use Puzzle\NewsletterBundle\Entity\Subscriber;

class SubscriberEvent extends Event
{
    /**
     * @var Subscriber
     */
    protected $subscriber;
    
    /**
     * @var array
     */
    protected $data;
	
	public function __construct(Subscriber $subscriber, array $data = null) {
	    $this->subscriber = $subscriber;
	    $this->data = $data;
	}
	
	public function getSubscriber() {
	    return $this->subscriber;
	}
	
	public function getData() {
	    return $this->data;
	}
}