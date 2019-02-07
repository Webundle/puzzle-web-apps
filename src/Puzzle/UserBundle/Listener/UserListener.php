<?php 

namespace Puzzle\UserBundle\Listener;

use Doctrine\ORM\EntityManager;
use Puzzle\AdminBundle\Event\AdminInstallationEvent;
use Puzzle\UserBundle\Entity\User;
use Puzzle\UserBundle\Event\UserEvent;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Translation\TranslatorInterface;
use Puzzle\UserBundle\Util\TokenGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author qwincy <qwincypercy@fermentuse.com>
 */
class UserListener
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
	
	public function onAdminInstalling(AdminInstallationEvent $event) {
	    $credentials = [
	        'email'            => 'jondoe@exemple.ci',
	        'username'         => 'johndoe',
	        'plainPassword'    => 'johndoe@password'
	    ];
	    
	    $user = $this->em->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);
	    if ($user === null) {
	        $user = new User();
	        $user->setFirstName('Doe');
	        $user->setLastName('John');
	        $user->setEmail($credentials['email']);
	        $user->setUsername($credentials['username']);
	        $user->setPassword(hash('sha512', $credentials['plainPassword']));
	        $user->setRoles(['ROLE_ADMIN']);
	        $user->setEnabled(true);
	        $user->setLocked(false);
	        
	        $this->em->persist($user);
	        $this->em->flush($user);
	    }
	    
	    $event->notifySuccess(sprintf(
	        'Admin account is created with username: <info>%s</info> and password: <info>%s</info>',
	        $credentials['username'],
	        $credentials['plainPassword']
	   ));
	}
	
	public function onCreating(UserEvent $event) {
	    $user = $event->getUser();
	    
	    if (null === $user->getPlainPassword()) {
	        $user->setPlainPassword(TokenGenerator::generate(8));
	    }
	    
	    $user->setPassword(hash('sha512', $user->getPlainPassword()));
	    
	    if (true === $user->isEnabled()) {
	        return;
	    }
	    
	    $user->setConfirmationToken(TokenGenerator::generate(12));
	    $this->em->flush($user);
	}
	
	public function onCreated(UserEvent $event) {
	    $user = $event->getUser();
	    $data = $event->getData();
	    
	    if (true === $user->isEnabled()) {
	        return;
	    }
	    
	    $subject = $this->translator->trans('user.create.title', ['%fullName%' => (string) $user], 'messages');
	    $body = $this->translator->trans('user.create.message', [
	        '%fullName%' => (string) $user,
	        '%username%' => $user->getUsername(),
	        '%plainPassword%' => $data['plainPassword'] ?? $user->getPlainPassword(),
	        '%confirmationUrl%' => $data['confirmationUrl']
	    ], 'messages');
	    
	    $this->sendEmail($this->registrationEmailAddress, $user->getEmail(), $subject, $body);
	}
	
	public function onUpdating(UserEvent $event) {
	    $user = $event->getUser();
	    
	    if (null !== $user->getPlainPassword()) {
	        $user->setPlainPassword(TokenGenerator::generate(8));
	        $user->setPassword(hash('sha512', $user->getPlainPassword()));
	        $user->setConfirmationToken(TokenGenerator::generate(12));
	        
	        $this->em->flush($user);
	    }
	    
	    if (true === $user->isEnabled()) {
	        return;
	    }
	}
	
	public function onUpdated(UserEvent $event) {
	    $user = $event->getUser();
	    $data = $event->getData();
	    
	    if (true === $user->isEnabled()) {
	        return;
	    }
	    
	    $subject = $this->translator->trans('user.update.title', ['%fullName%' => (string) $user], 'messages');
	    $body = $this->translator->trans('user.update.message', [
	        '%fullName%' => (string) $user,
	        '%username%' => $user->getUsername(),
	        '%plainPassword%' => $data['plainPassword'] ?? $user->getPlainPassword(),
	        '%confirmationUrl%' => $data['confirmationUrl']
	    ], 'messages');
	    
	    $this->sendEmail($this->registrationEmailAddress, $user->getEmail(), $subject, $body);
	}
	
	public function onEnabled(UserEvent $event) {
	    $user = $event->getUser();
	    $data = $event->getData();
	    
	    if (true === $user->isEnabled()) {
	        return;
	    }
	    
	    $subject = $this->translator->trans('user.enable.title', ['%fullName%' => (string) $user], 'messages');
	    $body = $this->translator->trans('user.enable.message', [
	        '%fullName%' => (string) $user,
	        '%confirmationUrl%' => $data['confirmationUrl']
	    ], 'messages');
	    
	    $this->sendEmail($this->registrationEmailAddress, $user->getEmail(), $subject, $body);
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
