<?php 

namespace Puzzle\CharityBundle\Listener;

use Doctrine\ORM\EntityManager;
use Puzzle\CharityBundle\Entity\Member;
use Puzzle\CharityBundle\Event\MemberEvent;
use Puzzle\UserBundle\Entity\User;
use Puzzle\UserBundle\Event\UserEvent;
use Puzzle\UserBundle\Util\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author qwincy <qwincypercy@fermentuse.com>
 */
class MemberListener
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
	 * @var string $registrationEmailAddress
	 */
	private $registrationEmailAddress;
	
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
	
	public function onCreatedUser(UserEvent $event) {
	    $user = $event->getUser();
	    
	    if (! $member = $this->em->getRepository(Member::class)->findOneBy(['email' => $user->getEmail()])) {
	        $member = new Member();
	        $member->setFirstName($user->getFirstName());
	        $member->setLastName($user->getLastName());
	        $member->setEmail($user->getEmail());
	        $member->setPhoneNumber($user->getPhoneNumber());
	        $member->setUser($user);
	        
	        $this->em->persist($member);
	        $this->em->flush($member);
	    }
	    
	    return;
	}
	
	public function onCreated(MemberEvent $event) {
	    $member = $event->getMember();
	    
	    if (! $user = $this->em->getRepository(User::class)->findOneBy(['email' => $member->getEmail()])) {
	        $user = new User();
	        $user->setEmail($member->getEmail());
	        $user->setUsername($member->getEmail());
	        $user->setFirstName($member->getFirstName());
	        $user->setLastName($member->getLastName());
	        
	        $user->setPlainPassword(TokenGenerator::generate(8));
	        $user->setPassword(hash('sha512', $user->getPlainPassword()));
	        $user->setRoles(array("ROLE_USER"));
	        $user->setConfirmationToken(TokenGenerator::generate(12));
	        
	        $this->em->persist($user);
	        $this->em->flush($user);
	        
	        $member->setUser($user);
	        $this->em->flush($member);
	        
	        $subject = $this->translator->trans('user.registration.email.subject', ['%fullName%' => (string) $user], 'messages');
	        $body = $this->translator->trans('user.registration.email.message', [
	            '%fullName%' => (string) $user,
	            '%username%' => $user->getUsername(),
	            '%plainPassword%' => $user->getPlainPassword(),
	            '%confirmationUrl%' => $this->router->generate('security_user_confirm_registration', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL)
	        ], 'messages');
	        
	        $this->sendEmail($this->registrationEmailAddress, $user->getEmail(), $subject, $body);
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
