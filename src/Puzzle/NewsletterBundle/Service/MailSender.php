<?php

namespace Puzzle\NewsletterBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Puzzle\NewsletterBundle\Entity\Subscriber;
use Puzzle\NewsletterBundle\Entity\Template;
use Doctrine\ORM\EntityManager;

class MailSender
{
	/**
	 *
	 * @var EntityManager $em
	 */
	protected $em;
	
	/**
	 * @var \Swift_Mailer $mailer
	 */
	protected $mailer;
	
	/**
	 * @var string
	 */
	protected $fromEmail;
	
	/**
	 * Constructor
	 * 
	 * @param Registry $doctrine
	 * @param string $fromEmail
	 */
	public function __construct(EntityManager $em, \Swift_Mailer $mailer, $fromEmail)
	{
		$this->em = $em;
		$this->mailer = $mailer;
		$this->fromEmail = $fromEmail;
	}
	
	public function sendBroadcast($subject, $templateSlug) {
	    $subscribers = $this->em->getRepository(Subscriber::class)->findAll();
	    $template = $this->em->getRepository(Template::class)->findOneBy(['slug' => $templateSlug]);
	    
	    $subject = $subject ?? $template->getName();
	    $body = $template->getContent();
	    $to = [];
	    
	    foreach ($subscribers as $subscriber) {
	        $to[] = $subscriber->getEmail();
	    }
	    
	    $this->send($subject, $this->fromEmail, $to, $body);
	}
	
	/**
     * 
     * Send Mail
     * 
     * @param string $subject
     * @param string $from
     * @param array $to
     * @param string $body
     * @param array $attachements
     */
    public function send(string $subject, string $from, $to, string $body = null, array $attachements = null)
    {
    	$message = \Swift_Message::newInstance()
			    	->setSubject($subject)
			    	->setFrom($this->fromEmail)
			    	->setTo($to)
			    	->setBody($body)
			    	->setContentType("text/html");
    	
    	if ($attachements) {
    		foreach ($attachements as $attachment) {
    			$message->attach(\Swift_Attachment::fromPath($attachment), "application/octet-stream");
    		}
    	}
    	
    	if (! $this->mailer->send($message, $failures)) {
    		return false;
    	}
    	
    	return true;
    }
}