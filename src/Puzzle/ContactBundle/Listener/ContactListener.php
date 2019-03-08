<?php
namespace Puzzle\ContactBundle\Listener;

use Puzzle\ContactBundle\Event\ContactEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Translation\TranslatorInterface;
use Puzzle\ContactBundle\Entity\Group;

class ContactListener 
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
	 * @var array $config
	 */
	private $config;
	
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
	
	/**
	 * Send message to new subscriber
	 * 
	 * @param ContactEvent $event
	 * @return boolean
	 */
	public function onAdd(ContactEvent $event)
	{
	    $subscriber = $event->getSubscriber();
	    $message = \Swift_Message::newInstance()
                	    ->setSubject("Bienvenue sur la plate-forme.")
                	    ->setFrom($this->fromEmail)
                	    ->setTo($subscriber->getEmail());
	    
        $body = "";
        $message->setBody($body)->setContentType("text/html");
        
	    if(! $this->mailer->send($message)){
	        return false;
	    }
	    
	    return true;
	}
	
	public function onCreated(ContactEvent $event) {
	    $contact = $event->getContact();
	    $data = $event->getData();
	    
	    // Contact Request
	    if (isset($data['subject']) && $data['subject'] && isset($data['message']) && $data['message']) {
	        $request = new \Puzzle\ContactBundle\Entity\Request();
	        $request->setSubject($data['subject']);
	        $request->setMessage($data['message']);
	        $request->setContact($contact);
	        
	        $this->em->persist($request);
	        $this->em->flush($request);
	    }
	    
	    // Contact Group
	    if (! empty($data['group'])) {
	        $group = $this->em->getRepository(Group::class)->find($data['group']);
	        
	        if ($group === null) {
	            $group = new Group();
	            $group->setName($data['group']);
	            $group->setDescription($data['group']);
	            
	            $this->em->persist($group);
	        }
	        
	        $group->addContact($contact);
	        $this->em->flush($group);
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