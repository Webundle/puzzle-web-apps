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
use Puzzle\CharityBundle\CharityEvents;
use Puzzle\NewsletterBundle\Entity\Template;
use Puzzle\CharityBundle\Entity\Group;

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
	
	/**
	 * Send mail when member is created
	 * @param MemberEvent $event
	 */
	public function onCreated(MemberEvent $event) {
	    $member = $event->getMember();
	    $data = $event->getData();
	    
	    if ($template = $this->em->getRepository(Template::class)->findOneBy(['event' => CharityEvents::CHARITY_MEMBER_CREATED])) {
	        $subject = $template->getName();
	        
	        $env = new \Twig_Environment(new \Twig_Loader_Array(['template' => $template->getContent()]));
	        $body = $env->render('template', array('member' => $member));
	        
	        $this->sendEmail($this->registrationEmailAddress, $member->getEmail(), $subject, $body);
	    }
	    
	    if (! empty($data['createAccount']) && $data['createAccount'] == 1) {
	        # User registration
	        $user = new User();
	        $user->setEmail($member->getEmail());
	        $user->setUsername($member->getEmail());
	        $user->setFirstName($member->getFirstName());
	        $user->setLastName($member->getLastName());
	        
	        $user->setPlainPassword(TokenGenerator::generate(8));
	        $user->setPassword(hash('sha512', $user->getPlainPassword()));
	        $user->setRoles(array(User::ROLE_DEFAULT));
	        $user->setConfirmationToken(TokenGenerator::generate(12));
	        
	        $this->em->persist($user);
	        $this->em->flush($user);
	        
	        // Associate member to user account
	        $member->setUser($user);
	        $this->em->flush($member);
	        
	        # User registration email
	        $subject = $this->translator->trans('user.registration.email.subject', ['%fullName%' => (string) $user], 'messages');
	        $body = $this->translator->trans('user.registration.email.message', [
	            '%fullName%' => (string) $user,
	            '%username%' => $user->getUsername(),
	            '%plainPassword%' => $user->getPlainPassword(),
	            '%confirmationUrl%' => $this->router->generate('security_user_confirm_registration', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL)
	        ], 'messages');
	        
	        $this->sendEmail($this->registrationEmailAddress, $user->getEmail(), $subject, $body);
	    }
	    
	    // Group
	    if (! empty($data['group'])) {
	        $group = $this->em->getRepository(Group::class)->find($data['group']);
	        
	        if ($group === null) {
	            $group = new Group();
	            $group->setName($data['group']);
	            $group->setDescription($data['group']);
	            
	            $this->em->persist($group);
	        }
	        
	        $group->addMember($member);
	        $this->em->flush($group);
	    }
	    
	    return;
	}
	
	
	/**
	 * Create member when user is registered
	 * @param UserEvent $event
	 */
	public function onCreatedUser(UserEvent $event) {
	    $user = $event->getUser();
	    
	    if ($this->em->getRepository(Member::class)->findOneBy(['email' => $user->getEmail()])) {
	        return;
	    }
	    
	    $member = new Member();
	    $member->setFirstName($user->getFirstName());
	    $member->setLastName($user->getLastName());
	    $member->setEmail($user->getEmail());
	    $member->setPhoneNumber($user->getPhoneNumber());
	    $member->setUser($user);
	    
	    $this->em->persist($member);
	    $this->em->flush($member);
	    
	    if ($template = $this->em->getRepository(Template::class)->findOneBy(['event' => CharityEvents::CHARITY_MEMBER_CREATED])) {
	        $subject = $template->getName();
	        
	        $env = new \Twig_Environment(new \Twig_Loader_Array(['template' => $template->getContent()]));
	        $body = $env->render('template', array('member' => $member));
	        
	        $this->sendEmail($this->registrationEmailAddress, $member->getEmail(), $subject, $body);
	    }
	    
	    return;
	}
	
	/**
	 * Enable member when a first donation is created
	 * @param MemberEvent $event
	 */
	public function onEnabled(MemberEvent $event) {
	    $member = $event->getMember();
	    
	    if ($member->isEnabled() === true) {
	        return;
	    }
	    
	    $member->setEnabled(true);
	    $member->setEnabledAt(new \DateTime());
	    
	    $this->em->flush($member);
	    
	    if ($template = $this->em->getRepository(Template::class)->findOneBy(['event' => CharityEvents::CHARITY_MEMBER_ENABLED])) {
	        $subject = $template->getName();
	        
	        $env = new \Twig_Environment(new \Twig_Loader_Array(['template' => $template->getContent()]));
	        $body = $env->render('template', array('member' => $member));
	        
	        $this->sendEmail($this->registrationEmailAddress, $member->getEmail(), $subject, $body);
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
