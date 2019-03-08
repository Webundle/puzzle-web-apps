<?php 

namespace Puzzle\CharityBundle\Listener;

use Doctrine\ORM\EntityManager;
use Puzzle\CharityBundle\CharityEvents;
use Puzzle\CharityBundle\Event\DonationEvent;
use Puzzle\NewsletterBundle\Entity\Template;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author qwincy <qwincypercy@fermentuse.com>
 */
class DonationListener
{
	/**
	 * @var EntityManager $em
	 */
	private $em;
	
	/**
	 * @var \Swift_Mailer $mailer
	 */
	private $mailer;
	
	/**
	 * @var \Twig_Environment $twig
	 */
	private $twig;
	
	/**
	 * @var Router
	 */
	private $router;
	
	/**
	 * @var TranslatorInterface
	 */
	private $translator;
	
	/**
	 * @var string $donationEmailAddress
	 */
	private $donationEmailAddress;
	
	/**
	 * @param EntityManager $em
	 * @param Router $router
	 * @param \Swift_Mailer $mailer
	 * @param string $fromEmail
	 */
	public function __construct(EntityManager $em, Router $router, \Swift_Mailer $mailer, \Twig_Environment $twig, TranslatorInterface $translator, string $registrationEmailAddress){
		$this->em = $em;
		$this->mailer = $mailer;
		$this->router = $router;
		$this->twig = $twig;
		$this->translator = $translator;
		$this->registrationEmailAddress = $registrationEmailAddress;
	}
	
	public function onPaid(DonationEvent $event) {
	    $donation = $event->getDonation();
	    
	    if ($donation->getPaidAmount() == 0) {
	        return;
	    }
	    
	    if ($template = $this->em->getRepository(Template::class)->findOneBy(['event' => CharityEvents::CHARITY_DONATION_PAID])) {
	        $subject = $template->getName();
	        
	        $env = new \Twig_Environment(new \Twig_Loader_Array(['template' => $template->getContent()]));
	        $body = $env->render('template', array('donation' => $donation));
	        
	        $this->sendEmail($this->registrationEmailAddress, $donation->getMember()->getEmail(), $subject, $body);
	    }
	    
	    return;
	}
	
	private function sendEmail($from, $to, string $subject, string $body) {
	    $message = \Swift_Message::newInstance()
                	    ->setFrom($from)
                	    ->setTo($to)
                	    ->setSubject($subject)
                	    ->setBody($body, 'text/html');
	    $this->mailer->send($message);
	}
}

?>
