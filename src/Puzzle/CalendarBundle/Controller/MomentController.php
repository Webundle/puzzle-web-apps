<?php

namespace Puzzle\CalendarBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Puzzle\CalendarBundle\Entity\Agenda;
use Puzzle\CalendarBundle\Entity\Moment;
use Puzzle\CalendarBundle\Form\Type\MomentFormType;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Util\MediaUtil;
use Puzzle\SchedulingBundle\Util\NotificationUtil;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Puzzle\CalendarBundle\Form\Type\MomentTranslationFormType;

/**
 * @author qwincy <qwincypercy@fermentuse.com>
 */
class MomentController extends Controller
{
    /***
     * Show Moments
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showListAction(Request $request){
        if ($this->isGranted("ROLE_CALENDAR_MANAGE") === false && $this->isGranted("ROLE_ADMIN") === false){
            $message = $this->get('translator')->trans('error.403', [], 'error');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message, 403);
            }
            
            throw new AccessDeniedHttpException();
        }
        $moments = $this->getDoctrine()->getRepository("CalendarBundle:Moment")->findAll();
        
//         $criteria = [];
//         $criteria[] = ['user', $this->getUser()->getId()];
//         $criteria[] = ['me.id', $this->getUser()->getId()];
        
//         if ($request->get('visibility') !== null) {
//             $criteria[] = ['visibility', $request->get('visibility')];
//         }
        
//     	$moments = $this->getDoctrine()->getRepository("CalendarBundle:Moment")->customFindBy(
//     	   null, ["members" => "me"], $criteria
//     	);
    	
  		if($request->getMethod() == "POST"){
  			$data = [];
  			
  			foreach ($moments as $moment){
  				$data[] = [
					'id' => $moment->getId(),
					'title' => $moment->getTitle(),
					'start' => $moment->getStartedAt()->format("Y-m-d H:i"),
					'end' => $moment->getEndedAt()->format("Y-m-d H:i"),
					'color' => $moment->getColor(),
					'url' => $this->generateUrl('admin_calendar_moment', array('id' => $moment->getId()))
  				];
  			}
  			
  			return new JsonResponse($data);
  		}
  		
    	return $this->render("CalendarBundle:Moment:list.html.twig", array(
    		'moments' => $moments,
    		'mode' => $request->get('mode')
    	));
    }
    
    /***
     * Show Moment
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Moment $moment){
        if ($this->isGranted("ROLE_CALENDAR_MANAGE") === false && $this->isGranted("ROLE_ADMIN") === false){
            $message = $this->get('translator')->trans('error.403', [], 'error');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message, 403);
            }
            
            throw new AccessDeniedHttpException();
        }
        
        $em = $this->getDoctrine()->getManager();
        $translations = $this->get('translation.translator')->findTranslations($moment);
        
    	return $this->render("CalendarBundle:Moment:view.html.twig", array(
    	    'moment' => $moment,
    	    'translations' => $translations,
    	    'recurrence' => $em->getRepository("SchedulingBundle:Recurrence")->findOneBy(NotificationUtil::constructTargetCriteria($moment)),
    	    'notification' => $em->getRepository("SchedulingBundle:Notification")->findOneBy(NotificationUtil::constructTargetCriteria($moment)),
    	));
    }

    /***
     * Create Moment Form
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request){
        if ($this->isGranted("ROLE_CALENDAR_MANAGE") === false && $this->isGranted("ROLE_ADMIN") === false){
            $message = $this->get('translator')->trans('error.400', [], 'error');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message, 400);
            }
            
            throw new BadRequestHttpException($message);
        }
        
        $moment = new Moment();
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(MomentFormType::class, $moment, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_calendar_moment_create')
        ]);
        $form->add('agenda', EntityType::class, array(
            'translation_domain' => 'messages',
            'label' => 'calendar.property.moment.agenda',
            'label_attr' => [
                'class' => 'uk-form-label'
            ],
            'class' => 'CalendarBundle:Agenda',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('a')
                          ->where("a.user = :user")
                          ->setParameter(':user', $this->getUser())
                          ->orderBy('a.name', 'ASC');
            },
            'choice_label' => 'name',
            'attr' => [
                'data-md-selectize' => true
            ],
        ));
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['moment_form'];
            $moment->setUser($this->getUser());
            $moment->setStartedAt(new \DateTime($data['startedAt']));
            $moment->setEndedAt(new \DateTime($data['endedAt']));
            $moment->setTags($moment->getTags() !== null ? explode(',', $moment->getTags()) : null);
            $moment->setEnableComments($moment->getEnableComments() == "on" ? true : false);
            $moment->setIsRecurrent($moment->getIsRecurrent() == "on" ? true : false);
            
            if ($moment->getAgenda() === null){
                $agenda = new Agenda();
                $agenda->setUser($this->getUser());
                $agenda->setName(Agenda::DEFAULT_NAME);
                $agenda->setVisibility(Agenda::VISIBILITY_PRIVATE);
                $moment->setAgenda($agenda);
                
                $em->persist($agenda);
            }
            
            if ($moment->getVisibility() === "share") {
                if ($request->request->get('members') !== null) {
                    $membersId = explode(',', $request->request->get('members'));
                    foreach ($membersId as $memberId) {
                        $member = $em->getRepository("UserBundle:User")->find($memberId);
                        $moment->addMember($member);
                    }
                }else {
                    $moment->setMembers($moment->getAgenda()->getMembers());
                }
            }
            
            
            if ($moment->getPicture() !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $moment->getPicture(),
                    'context' => MediaUtil::extractContext(Moment::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($moment) {
                        $moment->setPicture($filename);
                    }
                ]));
            }
            
            $em->persist($moment);
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            return $this->redirectToRoute('admin_calendar_moment', ['id' => $moment->getId()]);
        }
        
    	return $this->render("CalendarBundle:Moment:create.html.twig", array(
    	    'form' => $form->createView(),
    	    'mode' => $request->query->get('mode')
    	));
    }
    
    /***
     * Update Moment Form
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, Moment $moment){
        if ($this->isGranted("ROLE_CALENDAR_MANAGE") === false && $this->isGranted("ROLE_ADMIN") === false){
            $message = $this->get('translator')->trans('error.400', [], 'error');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message, 400);
            }
            
            throw new BadRequestHttpException($message);
        }
        
        $em = $this->getDoctrine()->getManager();
    	$oldPicture = $moment->getPicture();
    	
    	$form = $this->createForm(MomentFormType::class, $moment, [
    	    'method' => 'POST',
    	    'action' => $this->generateUrl('admin_calendar_moment_update', ['id' => $moment->getId()])
    	]);
    	$form->add('agenda', EntityType::class, array(
    	    'translation_domain' => 'messages',
    	    'label' => 'calendar.property.moment.agenda',
    	    'label_attr' => [
    	        'class' => 'uk-form-label'
    	    ],
    	    'class' => 'CalendarBundle:Agenda',
    	    'query_builder' => function (EntityRepository $er) {
    	    return $er->createQueryBuilder('a')
            	      ->where("a.user = :user")
            	      ->setParameter(':user', $this->getUser())
            	      ->orderBy('a.name', 'ASC');
    	    },
    	    'choice_label' => 'name',
    	    'attr' => [
    	        'data-md-selectize' => true
    	    ],
    	));
    	$form->handleRequest($request);
    	
    	if ($form->isSubmitted() === true && $form->isValid() === true) {
    	    $data = $request->request->all()['moment_form'];
    	    $moment->setStartedAt(new \DateTime($data['startedAt']));
    	    $moment->setEndedAt(new \DateTime($data['endedAt']));
    	    $moment->setTags($moment->getTags() !== null ? explode(',', $moment->getTags()) : null);
    	    $moment->setEnableComments($moment->getEnableComments() == "on" ? true : false);
    	    $moment->setIsRecurrent($moment->getIsRecurrent() == "on" ? true : false);
    	    
    	    if ($moment->getVisibility() === "share") {
    	        if ($request->request->get('members') !== null) {
    	            $membersId = explode(',', $request->request->get('members'));
    	            foreach ($membersId as $memberId) {
    	                $member = $em->getRepository("UserBundle:User")->find($memberId);
    	                $moment->addMember($member);
    	            }
    	        }else {
    	            $moment->setMembers($moment->getAgenda()->getMembers());
    	        }
    	    }
    	    
    	    if ($moment->getPicture() !== null && $moment->getPicture() !== $oldPicture) {
    	        $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
    	            'path' => $moment->getPicture(),
    	            'context' => MediaUtil::extractContext(Moment::class),
    	            'preserve-file' => isset($data['preserve-file']) ?: true,
    	            'user' => $this->getUser(),
    	            'closure' => function($filename) use ($moment) {
    	               $moment->setPicture($filename);
    	            }
    	        ]));
    	    }
    	    
    	    $em->persist($moment);
    	    $em->flush();
    	    
    	    if ($request->isXmlHttpRequest() === true) {
    	        return new JsonResponse(['status' => true]);
    	    }
    	    
    	    return $this->redirectToRoute('admin_calendar_moment', ['id' => $moment->getId()]);
    	}
    	
    	return $this->render("CalendarBundle:Moment:update.html.twig", array(
    		'moment' => $moment,
    	    'form' => $form->createView(),
    	    'recurrence' => $em->getRepository("SchedulingBundle:Recurrence")->findOneBy(NotificationUtil::constructTargetCriteria($moment)),
    	    'notification' => $em->getRepository("SchedulingBundle:Notification")->findOneBy(NotificationUtil::constructTargetCriteria($moment)),
    	));
    }
   
    /***
     * Translate service
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function translateAction(Request $request, Moment $moment) {
        if ($this->isGranted("ROLE_CALENDAR_MANAGE") === false && $this->isGranted("ROLE_ADMIN") === false){
            $message = $this->get('translator')->trans('error.400', [], 'error');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message, 400);
            }
            
            throw new BadRequestHttpException($message);
        }
        
        $form = $this->createForm(MomentTranslationFormType::class, $moment, ['method' => 'POST'])
        ->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            
            $data = $request->request->get('translate');
            $locale = $em->getRepository("AdminBundle:Language")->find($data['locale']);
            $translatableFields = explode(',', $this->getParameter('calendar')['moment_translatable_fields']);
            
            foreach ($translatableFields as $field){
                $this->get('translation.translator')->translate($moment, $locale->getName(), $field, $data[$field]);
            }
            
            return $this->redirectToRoute('admin_calendar_moment', ['id' => $moment->getId()]);
        }
        
        return $this->render("CalendarBundle:Moment:translate.html.twig", array(
            'moment' => $moment,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Remove Moment
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Request $request, Moment $moment){
        if ($this->isGranted("ROLE_CALENDAR_MANAGE") === false && $this->isGranted("ROLE_ADMIN") === false){
            $message = $this->get('translator')->trans('error.400', [], 'error');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message, 400);
            }
            
            throw new BadRequestHttpException($message);
        }
        
    	$em = $this->getDoctrine()->getManager();
    	$em->remove($moment);
    	$em->flush();
    	
    	if ($request->isXmlHttpRequest() === true) {
    	    return new JsonResponse(['status' => true]);
    	}
    
    	return $this->redirectToRoute('admin_calendar_moments', array('mode' => $request->get('mode')));
    }
    
    /***
     * Remove moments
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeListAction(Request $request){
        if ($this->isGranted("ROLE_CALENDAR_MANAGE") === false && $this->isGranted("ROLE_ADMIN") === false){
            $message = $this->get('translator')->trans('error.400', [], 'error');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message, 400);
            }
            
            throw new BadRequestHttpException($message);
        }
        
        $list = explode(',', $request->request->get('ids'));
        $em = $this->getDoctrine()->getManager();
        
        foreach ($list as $id){
            if($id != null){
                $moment = $em->getRepository("CalendarBundle:Moment")->find($id);
                $em->remove($moment);
            }
        }
        
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(['status' => true]);
        }
        
        return $this->redirectToRoute('admin_calendar_moments', array('mode' => $request->get('mode')));
    }
}
