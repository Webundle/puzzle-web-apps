<?php
namespace Puzzle\MailBundle\Controller;

use Puzzle\MailBundle\MailEvents;
use Puzzle\MailBundle\Entity\Mail;
use Puzzle\MailBundle\Event\MailEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Puzzle\MailBundle\MailCommands;
use Puzzle\SchedulingBundle\Event\SchedulingEvent;
use Puzzle\SchedulingBundle\SchedulingEvents;
use Puzzle\SchedulingBundle\Entity\Notification;

/**
 * 
 * @author qwincy
 *
 */
class MailController extends Controller
{
	/***
	 * Show Mails
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function showListAction(Request $request){
		$mails = $this->getDoctrine()->getRepository("MailBundle:Mail")->findBy(array(), array('updatedAt' => 'DESC'));
		return $this->render("MailBundle:Mail:list.html.twig", array(
			'mails' => $mails,
		    'bundleName' => $this->getParameter("newsletter")['content']['bundle_name'],
		    'config' => $this->getParameter("newsletter")['content']['mail'],
		));
	}
	
	/***
	 * Show Mail
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function showAction(Request $request, Mail $mail) {
		$em = $this->getDoctrine()->getManager();
		
		$notification = $em->getRepository("SchedulingBundle:Notification")->findOneBy(array(
				'targetAppName' => "newsletter",
				'targetEntityName' => "mail",
				'targetEntityId' => $mail->getId()
		));
		
		$recurrence = $em->getRepository("SchedulingBundle:Recurrence")->findOneBy(array(
				'targetAppName' => "newsletter",
				'targetEntityName' => "mail",
				'targetEntityId' => $mail->getId()
		));
		
		$receivers = [];
		if ($mail->getReceivers() !== null) {
		    $list = $this->get('admin.util.doctrine_query_parameter')->formatForInClause($mail->getReceivers());
		    $subscribers = $this->getDoctrine()->getRepository("MailBundle:Subscriber")->customFindBy(
		        null, null, [['id', null, 'IN '.$list]]
		        );
		    
		    foreach ($subscribers as $subscriber){
		        $receivers[] = $subscriber->getEmail();
		    }
		}
		
		$attachments = [];
		
		if ($mail->getAttachments() !== null) {
		    $list = $this->get('admin.util.doctrine_query_parameter')->formatForInClause($mail->getAttachments());
		    $attachments = $this->getDoctrine()->getRepository("MailBundle:Subscriber")->customFindBy(
		        null, null, [['id', null, 'IN '. $list]]
		    );
		}
		
		return $this->render("MailBundle:Mail:view.html.twig", array(
			'recurrence' => $recurrence,
			'attachments' => $attachments,
			'notification' => $notification,
			'mail' => $mail,
			'receivers' => $receivers,
		    'bundleName' => $this->getParameter("newsletter")['content']['bundle_name'],
		    'config' => $this->getParameter("newsletter")['content']['mail'],
		));
	}
	
	/***
	 * Show Mail
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function previewAction(Request $request, Mail $mail) {
	    return new Response($mail->getBody());
	}
	
	/***
	 * Create Mail Form
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function createFormAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		if($templateId = $request->query->get('template')){
			$template = $em->getRepository("MailBundle:Template")->find($templateId);
			$templates = $em->getRepository('MailBundle:Template')->findBYNot(['id' => $templateId]);
		}else{
			$template = null;
			$templates = $em->getRepository('MailBundle:Template')->findAll();
		}
		
		return $this->render("MailBundle:Mail:create.html.twig", array(
			'template' => $template,
			'templates' => $templates,
		    'bundleName' => $this->getParameter("newsletter")['content']['bundle_name'],
		    'config' => $this->getParameter("newsletter")['content']['mail'],
		));
	}
    
    /**
     * Create a mail
     * 
     * @param string $idMail
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createCallbackAction(Request $request) {
    	$data = $request->request->all();
    	$em = $this->getDoctrine()->getManager();
    	
    	$mail = new Mail();
    	$mail->setSubject($data['subject']);
    	$mail->setBody($data['body']);
    	$mail->setAttachments($data['attachments'] ? explode(',', $data['attachments']) : null);
    	$mail->setTag(isset($data['tag']) ? $data['tag'] : null);
    	
    	if (isset($data['receivers'])) {
    		$list = explode(',', $data['receivers']);
    		$receivers = [];
    		
    		foreach ($list as $item) {
    			$item = str_replace('(groupe)', '', $item);
    			$subscriber = $em->getRepository("MailBundle:Subscriber")->find($item);
    			if ($subscriber !== null) {
    				$receivers[] = $subscriber->getId();
    			}else{
    				$group = $em->getRepository("MailBundle:Group")->findOneBy(array('name' => $item));
    				
    				if ($group !== null) {
    					foreach ($group->getSubscribers() as $subscriber){
    						$receivers[] = $subscriber;
    					}
    				}else {
    					$receivers[] = $item;
    				}
    			}
    		}
    	}
    	
    	$mail->setReceivers($receivers);
    	$em->persist($mail);
    	
    	if ($data['tag'] !== null) {
    		switch ($data['tag']) {
    			case Mail::MAIL_SENT:
    				$subject = $mail->getSubject();
    				$body = $mail->getBody();
    				$to = $mail->getReceivers();
    				$attachements = $mail->getAttachments();
    				$from = $this->getParameter('admin_mailemail');
    				
    				$status = $this->get('newsletter.mail_manager')->send($subject, $from, $to, $body, $attachements);
    				if($status){
    					$mail->setSentAt(new \DateTime());
    				}
    				
    				break;
    			case Mail::MAIL_SCHEDULED:
    			    $data['targetEntityId'] = $mail->getId();
    			    $data['targetEntityName'] = "mail";
    			    $data['targetAppName'] = "newsletter";
    			    
    			    $data['notification_command'] = MailCommands::SEND_MAIL;
    			    $data['notification_command_args'] = [$mail->getId()];
    			    $data['notification_channel'] = Notification::CHANNEL_EMAIL;
    			    $data['notification_unity'] = "PT1M";
    			    $data['notification_intervale'] = 1;
    			    
    				$event = new SchedulingEvent($data);
    				$this->get('event_dispatcher')->dispatch(SchedulingEvents::SCHEDULE, $event);
    				
    				break;
    			default:
    				break;
    		}
    	}
    	
    	$em->flush();
    	
    	if ($request->isXmlHttpRequest() === true) {
    	    return new JsonResponse(['status' => true]);
    	}
    	
    	return $this->redirect($this->generateUrl('admin_mailmails'));
    }
    
    
    /***
     * Update Mail Form
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateFormAction(Request $request, Mail $mail) {
    	$em = $this->getDoctrine()->getManager();
    	
    	$notification = $em->getRepository("SchedulingBundle:Notification")->findOneBy(array(
    		'targetAppName' => "newsletter",
    		'targetEntityName' => "mail",
    		'targetEntityId' => $mail->getId()
    	));
    	
    	$recurrence = $em->getRepository("SchedulingBundle:Recurrence")->findOneBy(array(
    		'targetAppName' => "newsletter",
    		'targetEntityName' => "mail",
    		'targetEntityId' => $mail->getId()
    	));
    	
    	$receivers = [];
    	if ($mail->getReceivers() !== null) {
    	    $list = $this->get('admin.util.doctrine_query_parameter')->formatForInClause($mail->getReceivers());
    	    $subscribers = $this->getDoctrine()->getRepository("MailBundle:Subscriber")->customFindBy(
    	        null, null, [['id', null, 'IN '.$list]]
    	    );
    	    
    	    foreach ($subscribers as $subscriber){
    	        $receivers[] = $subscriber->getEmail();
    	    }
    	}
    	
    	$attachments = null;
    	if ($mail->getAttachments() !== null && count($mail->getAttachments()) > 0) {
    	    $list = $this->get('admin.util.doctrine_query_parameter')->formatForInClause($mail->getAttachments());
    	    $attachments = $this->getDoctrine()->getRepository("MediaBundle:File")->customFindBy(
    	        null, null, [['id', null, 'IN '.$list]]
    	     );
    	}
    	
    	return $this->render("MailBundle:Mail:update.html.twig", array(
    	    'mail' => $mail,
    	    'receivers' => $receivers,
    	    'attachments' => $attachments,
    		'recurrence' => $recurrence,
    		'notification' => $notification,
    	    'bundleName' => $this->getParameter("newsletter")['content']['bundle_name'],
    	    'config' => $this->getParameter("newsletter")['content']['mail'],
    	));
    }
    
    /**
     * Edit a mail
     * 
     * @param unknown $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateCallbackAction($id, Request $request)
    {
    	$tag = $request->query->get('tag');
    	$data = $request->request->all();
    	$em = $this->getDoctrine()->getManager();
    	
    	$mail = $em->getRepository("MailBundle:Mail")->find($id);
    	$mail->setSubject($data['subject']);
    	$mail->setBody($data['body']);
    	$mail->setTemplate(isset($data['template']) && $data['template']?  $data['template']: null);
    	
    	if(isset($data['receivers'])){
    		$list = explode(',', $data['receivers']);
    		$receivers = [];
    		
    		foreach ($list as $item){
    			$item = str_replace('(groupe)', '', $item);
    			$subscriber = $em->getRepository("MailBundle:Subscriber")->find($item);
    			if ($subscriber) {
    				$receivers[] = $subscriber->getEmail();
    			}else{
    				$group = $em->getRepository("MailBundle:GroupSubscriber")->findOneBy(array('name' => $item));
    				if($group){
    					foreach ($group->getSubscribers() as $subscriber){
    						$receivers[] = $subscriber->getEmail();
    					}
    				}else {
    					$receivers[] = $item;
    				}
    			}
    		}
    	}
    	
    	$mail->setReceivers($receivers);
    	$mail->setAttachments($data['attachments_to_add'] ? explode(',', $data['attachments_to_add']) : null);
    	
    	if($mail->getTag() != $tag){
    		$mail->setTag($tag);
    		
    		switch ($tag){
    			case Mail::MAIL_SENT:
    				
    				$dispatcher = new EventDispatcher();
    				$event = new MailEvent($mail, $data);
    				$listener = $this->get('newsletter.mail_listener');
    				
    				$dispatcher->addListener(MailEvents::CREATE_MAIL, array($listener, 'onUnschedule'));
    				$dispatcher->dispatch(MailEvents::CREATE_MAIL, $event);
    				
    				$subject = $mail->getSubject();
    				$body = $mail->getBody();
    				$to = $mail->getReceivers();
    				$attachements = $mail->getAttachments();
    				$from = $this->getParameter('newsletter.email');
    				
    				$status = $this->get('newsletter.mail_manager')->sender($subject, $from, $to, $body, $attachements);
    				if($status){
    					$mail->setSentAt(new \DateTime());
    				}
    				
    				break;
    			case Mail::MAIL_SCHEDULED:
    				$dispatcher = new EventDispatcher();
    				$event = new MailEvent($mail, $data);
    				$listener = $this->get('newsletter.mail_listener');
    				
    				$dispatcher->addListener(MailEvents::CREATE_MAIL, array($listener, 'onSchedule'));
    				$dispatcher->dispatch(MailEvents::CREATE_MAIL, $event);
    				
    				break;
    			default:
    				$dispatcher = new EventDispatcher();
    				$event = new MailEvent($mail, $data);
    				$listener = $this->get('newsletter.mail_listener');
    				
    				$dispatcher->addListener(MailEvents::CREATE_MAIL, array($listener, 'onSchedule'));
    				$dispatcher->dispatch(MailEvents::CREATE_MAIL, $event);
    				break;
    		}
    	}
    	
    	$em->flush();
    	
    	if ($request->isXmlHttpRequest() === true) {
    	    return new JsonResponse(['status' => true]);
    	}
    	
    	return $this->redirect($this->generateUrl('admin_mailmails'));
    }
    
    
    
    /**
     * Remove a mail
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Request $request, Mail $mail){
    	$em = $this->getDoctrine()->getManager();
    	$em->remove($mail);
    	$em->flush();
    	
    	if ($request->isXmlHttpRequest() === true) {
    	    return new JsonResponse(['status' => true]);
    	}
    
    	return $this->redirect($this->generateUrl('admin_mailmails'));
    }
    
    
    /***
     * Remove mails
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeListAction(Request $request)
    {
    	$list = explode(',', $request->request->get('ids'));
    	$em = $this->getDoctrine()->getManager();
    	
    	foreach ($list as $id){
    		if($id != null){
    			$mail = $em->getRepository("MailBundle:Mail")->find($id);
    			$em->remove($mail);
    		}
    	}
    	
    	$em->flush();
    	
    	if ($request->isXmlHttpRequest() === true) {
    	    return new JsonResponse(['status' => true]);
    	}
    	
    	return $this->redirect($this->generateUrl('admin_mailmails'));
    }
    
}
