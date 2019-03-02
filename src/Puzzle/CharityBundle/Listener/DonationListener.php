<?php 

namespace Puzzle\CharityBundle\Listener;

use Doctrine\ORM\EntityManager;
use Puzzle\CharityBundle\Entity\Donation;
use Puzzle\CharityBundle\Event\DonationEvent;
use Puzzle\UserBundle\Entity\User;
use Puzzle\UserBundle\Event\UserEvent;
use Puzzle\UserBundle\Util\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Puzzle\NewsletterBundle\Entity\Template;
use Puzzle\CharityBundle\CharityEvents;

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
	public function __construct(EntityManager $em, Router $router, \Swift_Mailer $mailer, \Twig_Environment $twig, TranslatorInterface $translator, array $config){
		$this->em = $em;
		$this->mailer = $mailer;
		$this->router = $router;
		$this->twig = $twig;
		$this->translator = $translator;
		$this->config = $config;
	}
	
	public function onCreatedMember(UserEvent $event) {
	    $user = $event->getUser();
	    
	    if (! $donation = $this->em->getRepository(Donation::class)->findOneBy(['email' => $user->getEmail()])) {
	        $donation = new Donation();
	        $donation->setFirstName($user->getFirstName());
	        $donation->setLastName($user->getLastName());
	        $donation->setEmail($user->getEmail());
	        $donation->setPhoneNumber($user->getPhoneNumber());
	        $donation->setUser($user);
	        
	        $this->em->persist($donation);
	        $this->em->flush($donation);
	    }
	    
	    return;
	}
	
	public function onCreated(DonationEvent $event) {
	    $donation = $event->getDonation();
	    $member = $donation->getMember();
	    
	    if ($member->isEnabled() === false) {
	        $member->setEnabled(true);
	        $member->setEnabledAt(new \DateTime());
	        
	        $this->em->flush($member);
	    }
	    
	    if ($template = $this->em->getRepository(Template::class)->findOneBy(['event' => CharityEvents::CHARITY_DONATION_CREATED])) {
	        $subject = $template->getName();
	        
	        $env = new \Twig_Environment(new \Twig_Loader_Array(['template' => $template->getContent()]));
	        $body = $env->render('template', array('donation' => $donation));
	        
	        $this->sendEmail($this->config['website']['email'], $member->getEmail(), $subject, $body);
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
