<?php 

namespace Puzzle\MailBundle\Listener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Puzzle\MailBundle\Event\ContactEvent;
use Puzzle\UserBundle\Entity\Notification;

class ContactListener
{
	/**
	 * @var \Swift_Mailer $mailer
	 */
    private $mailer;
    
    /**
     * @var string $toEmail
     */
    private $toEmail;
	
	public function __construct(\Swift_Mailer $mailer, $toEmail) {
		$this->mailer = $mailer;
		$this->toEmail = $toEmail;
	}
	
	/**
	 * Contact created
	 * 
	 * @param ContactEvent $event
	 */
	public function onCreate(ContactEvent $event)
	{
	    $contact = $event->getContact();
		$message = \Swift_Message::newInstance()
                		->setSubject($contact->getSubject())
                		->setFrom($this->fromEmail)
                		->setTo($this->toEmail)
                		->setBody($contact->getMessage())
                		->setContentType("text/html");
		
		if($attachements){
		    foreach ($attachements as $attachment){
		        $message->attach(\Swift_Attachment::fromPath($attachment), "application/octet-stream");
		    }
		}
		
		if(! $this->container->get('mailer')->send($message, $failures)){
		    return false;
		}
		
		return true;
	}
	
}
