<?php
namespace Puzzle\ContactBundle\Listener;

use Puzzle\ContactBundle\Event\ContactEvent;

class ContactListener {

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
	 * @param ContactEvent $event
	 * @return boolean
	 */
	public function onAdd(ContactEvent $event)
	{
	    $subscriber = $event->getSubscriber();
	    $message = \Swift_Message::newInstance()
                	    ->setSubject("Bienvenue sur la plate-forme.")
                	    ->setFrom($this->fromEmail)
                	    ->setTo($subscriber->getEmail());
	    
        $body = "";
        $message->setBody($body)->setContentType("text/html");
        
	    if(! $this->mailer->send($message)){
	        return false;
	    }
	    
	    return true;
	}
	
	
}