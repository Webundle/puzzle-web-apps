<?php

namespace Puzzle\MailBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Puzzle\MediaBundle\Service\UploadManager;
use Puzzle\MailBundle\Entity\Mail;
use Puzzle\SchedulingBundle\Service\SchedulingCron;
use Puzzle\SchedulingBundle\Service\SchedulingManager;
use Puzzle\SchedulingBundle\Service\SchedulingTools;

class MailManager
{
	/**
	 * @var string
	 */
	protected $fromEmail;
	
	/**
	 *
	 * @var Registry $doctrine
	 */
	protected $doctrine;
	
	/**
	 *
	 * @var EntityManager $em
	 */
	protected $em;
	
	/**
	 *
	 * @var SchedulingManager $schedulingManager
	 */
	protected $schedulingManager;
	
	/**
	 *
	 * @var SchedulingCron $schedulingCron
	 */
	protected $schedulingCron;
	
	/**
	 *
	 * @var SchedulingTools $schedulingTools
	 */
	protected $schedulingTools;
	
	/**
	 * Constructor
	 * 
	 * @param Registry $doctrine
	 * @param SchedulingManager $schedulingManager
	 * @param SchedulingTools $schedulingTools
	 * @param UploadManager $uploadManager
	 */
	public function __construct(Registry $doctrine, SchedulingManager $schedulingManager, SchedulingCron $schedulingCron, SchedulingTools $schedulingTools, $fromEmail)
	{
		$this->doctrine = $doctrine;
		$this->em = $this->doctrine->getManager('newsletter');
		$this->schedulingManager = $schedulingManager;
		$this->schedulingCron = $schedulingCron;
		$this->schedulingTools = $schedulingTools;
		$this->fromEmail = $fromEmail;
	}

	/**
	 * Apply scheduling
	 *
	 * @param string $mailId
	 */
	public function apply(string $mailId)
	{
	    $em = $this->doctrine->getManager('scheduling');
	    $mail = $this->em->getRepository("MailBundle:Mail")->find($mailId);
	    $recurrence = $em->getRepository("SchedulingBundle:Recurrence")->findOneBy(array(
	        'targetAppName' => "newsletter",
	        'targetEntityName' => "mail",
	        'targetEntityId' => $mail->getId(),
	    ));
	    $notification = $em->getRepository("SchedulingBundle:Notification")->findOneBy(array(
	        'targetAppName' => "newsletter",
	        'targetEntityName' => "mail",
	        'targetEntityId' => $mail->getId()
	    ));
	    
	    if($recurrence){
	        if(! in_array($now->format('w'), $recurrence->getExcludedDays())){
	        	$subject = $mail->getSubject();
	        	$body = $mail->getBody();
	        	$to = $mail->getReceivers();
	        	$attachements = $mail->getAttachments();
	        	
	        	if($this->send($subject, $this->fromEmail, $to, $body, $attachements)){
	        		// Clone mail
	        		$pastMail = new Mail();
	        		$pastMail->setSubject($mail->getSubject());
	        		$pastMail->setReceivers($mail->getReceivers());
	        		$pastMail->setBody($mail->getBody());
	        		$pastMail->setTag(Mail::MAIL_SENT);
	        		$pastMail->setAttachments($mail->getAttachments());
	        		$this->em->persist($pastMail);
	        	}
	        }
	        
	        $now = new \DateTime();
	        $dueAt = $recurrence->getDueAt();
	        
	        if($recurrence->getDueAt() <= $now){
	            $this->schedulingCron->remove($recurrence->getJob());
	            $em->remove($recurrence);
	            $em->remove($notification);
	        }else{
	            $intervale = $this->schedulingTools->convertIntervale($recurrence->getIntervale(), $recurrence->getUnity());
	            $nextRunAt = $recurrence->getNextRunAt()->add(new \DateInterval("PT".$intervale."M"));
	            
	            if($nextRunAt->getTimestamp() <= $recurrence->getDueAt()->getTimestamp()){
	                $recurrence->setNextRunAt($nextRunAt);
	                
	                $startedAt = $mail->getStartedAt();
	                $endedAt = $mail->getEndedAt();
	                $diff = $endedAt->getTimestamp() - $startedAt->getTimestamp();
	                $diff += $nextRunAt->getTimestamp();
	                $date = new \DateTime();
	                $date->setTimestamp($diff);
	                
	                $nextRunAt = $recurrence->getNextRunAt();
	                $mail->setStartedAt($nextRunAt);
	                $mail->setEndedAt($date);
	                
	                $intervale = $this->schedulingTools->convertIntervale($notification->getIntervale(), $notification->getUnity());
	                $nextRunAt = $nextRunAt->sub(new \DateInterval("PT".$intervale."M"));
	                $notification->setNextRunAt($nextRunAt);
	                
	                $this->schedulingManager->schedule("newsletter", "mail", $mail->getId());
	            }
	        }
	    }
	    
	    $em->flush();
	    $this->em->flush();
	    
	    return;
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
    public function send(string $subject, string $from, array $to, string $body, array $attachements)
    {
    	$message = \Swift_Message::newInstance()
			    	->setSubject($subject)
			    	->setFrom($this->fromEmail)
			    	->setTo($to)
			    	->setBody($body)
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