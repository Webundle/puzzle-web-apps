<?php

namespace Puzzle\ExpertiseBundle\Listener;

use Doctrine\ORM\EntityManager;
use Puzzle\ExpertiseBundle\Entity\Contact;
use Puzzle\ExpertiseBundle\Event\ContactEvent;
use Puzzle\UserBundle\Entity\User;
use Puzzle\UserBundle\Event\UserEvent;
use Puzzle\UserBundle\Util\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Puzzle\ExpertiseBundle\ExpertiseEvents;
use Puzzle\NewsletterBundle\Entity\Template;
use Puzzle\ExpertiseBundle\Entity\Group;

/**
 * @author qwincy <qwincypercy@fermentuse.com>
 */
class ContactListener
{
	/**
	 * @var \Swift_Mailer $mailer
	 */
	private $mailer;

	/**
	 * @var string $toEmailAddress
	 */
	private $toEmailAddress;

	/**
	 * @var bool $sendContactByMail
	 */
	private $sendContactByMail;

	/**
	 * @param \Swift_Mailer $mailer
	 * @param string $fromEmail
	 * @param bool $sendContactByMail
	 */
	public function __construct(\Swift_Mailer $mailer, bool $sendContactByMail = false, string $toEmailAddress){
		$this->mailer = $mailer;
		$this->toEmailAddress = $toEmailAddress;
		$this->$sendContactByMail = $sendContactByMail;
	}

	/**
	 * Send mail when contact is created
	 * @param ContactEvent $event
	 */
	public function onCreated(ContactEvent $event) {
			if ($sendContactByMail === false) {
					return;
			}

	    $contact = $event->getContact();
			$subject = $contact->getSubject();
			$body = $contact->getMessage();
			$from = $contact->getEmail();

			$message = \Swift_Message::newInstance()
                	    ->setFrom($from)
                	    ->setTo($this->toEmailAddress)
                	    ->setSubject($subject)
                	    ->setBody($body, 'text/html');
	    $this->mailer->send($message);

	    return;
	}
}

?>
