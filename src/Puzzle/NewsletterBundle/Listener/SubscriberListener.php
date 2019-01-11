<?php
namespace Puzzle\NewsletterBundle\Listener;

use Puzzle\NewsletterBundle\Event\SubscriberEvent;

class SubscriberListener {

	/**
	 * @var \Swift_Mailer $mailer
	 */
    protected $mailer;
    
    /**
     * @var string
     */
	protected $fromEmail;
	
	public function __construct(\Swift_Mailer $mailer, $fromEmail){
	    $this->mailer = $mailer;
	    $this->fromEmail = $fromEmail;
	}
	
	/**
	 * Send message to new subscriber
	 * 
	 * @param SubscriberEvent $event
	 * @return boolean
	 */
	public function onCreate(SubscriberEvent $event)
	{
	    $subscriber = $event->getSubscriber();
	    $data = $event->getData();
	    
	    $message = \Swift_Message::newInstance()
	                    ->setSubject($data['subject'])
                	    ->setFrom($this->fromEmail)
                	    ->setTo($subscriber->getEmail())
                	    ->setBody($data['body'])
	                    ->setContentType("text/html");
        
	    if (! $this->mailer->send($message)) {
	        return false;
	    }
	    
	    return true;
	}
	
	
}