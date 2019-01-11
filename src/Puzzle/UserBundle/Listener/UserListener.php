<?php 

namespace Puzzle\UserBundle\Listener;

use Doctrine\ORM\EntityManager;
use Puzzle\AdminBundle\Event\AdminInstallationEvent;
use Puzzle\UserBundle\Entity\User;
use Puzzle\UserBundle\Event\UserEvent;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Translation\TranslatorInterface;

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
	 * @var string $fromEmail
	 */
	private $fromEmail;
	
	/**
	 * @param EntityManager $em
	 * @param Router $router
	 * @param \Swift_Mailer $mailer
	 * @param string $fromEmail
	 */
	public function __construct(EntityManager $em, Router $router, \Swift_Mailer $mailer, \Twig_Environment $twig, TranslatorInterface $translator, string $fromEmail){
		$this->em = $em;
		$this->mailer = $mailer;
		$this->router = $router;
		$this->twig = $twig;
		$this->translator = $translator;
		$this->fromEmail = $fromEmail;
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
	
	/**
	 * Create user
	 * @param UserEvent $event
	 */
	public function onCreate(UserEvent $event) {
		$user = $event->getUser();
		
		return $this->mailer->send(array(
	        'subject' => $this->translator->trans('user.create.confirm_subject', [], 'messages'),
	        'to' => $user->getEmail(),
	        'from' => $this->fromEmail,
	        'body' => $this->twig->render('AdminBundle:User:confirm_create_user.html.twig', [
	            'user' => $user,
	            'confirmationUrl' => $this->router->generate('app_user_confirm', ['token' => $user->getConfirmationToken()])
	        ])
	    ));
	}
	
	/**
	 * Update password
	 * @param UserEvent $event
	 */
	public function onUpdatePassword(UserEvent $event) {
	    $user = $event->getUser();
	    $user->setPassword(hash('sha512', $user->getPlainPassword()));
	    
	    $this->em->flush($user);
	    return;
	}
}

?>
