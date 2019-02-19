<?php
namespace Puzzle\CalendarBundle\Controller;

use Puzzle\CalendarBundle\Entity\Agenda;
use Puzzle\CalendarBundle\Form\Type\AgendaCreateType;
use Puzzle\CalendarBundle\Form\Type\AgendaUpdateType;
use Puzzle\SchedulingBundle\Util\NotificationUtil;
use Puzzle\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\CalendarBundle\Entity\Moment;
use Puzzle\SchedulingBundle\Entity\Recurrence;
use Puzzle\SchedulingBundle\Entity\Notification;
use Puzzle\CalendarBundle\Form\Type\MomentCreateType;
use Puzzle\CalendarBundle\Form\Type\MomentUpdateType;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Util\MediaUtil;
use Puzzle\SchedulingBundle\Event\SchedulingEvent;
use Puzzle\SchedulingBundle\SchedulingEvents;
use Puzzle\SchedulingBundle\Command\ApplyRecurrenceCommand;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class AdminController extends Controller
{
    public function listAgendasAction(Request $request) {
        return $this->render("AdminBundle:Calendar:list_agendas.html.twig", array(
            'agendas' => $this->getDoctrine()->getRepository(Agenda::class)->findAll()
        ));
    }
    
    public function showAgendaAction(Request $request, Agenda $agenda) {
    	return $this->render("AdminBundle:Calendar:show_agenda.html.twig", array(
    	    'agenda' => $agenda
    	));
    }
    
    public function createAgendaAction(Request $request) {
        $agenda = new Agenda();
        
        $form = $this->createForm(AgendaCreateType::class, $agenda, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_calendar_agenda_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($agenda);
            
            if ($agenda->getVisibility() === "share") {
                if ($request->request->get('members') !== null) {
                    $membersId = explode(',', $request->request->get('members'));
                    foreach ($membersId as $memberId) {
                        $member = $em->getRepository(User::class)->find($memberId);
                        $agenda->addMember($member);
                    }
                }
            }
            
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => $agenda->getName()], 'messages'));
            return $this->redirectToRoute('admin_calendar_agenda_list');
        }
        
    	return $this->render("AdminBundle:Calendar:create_agenda.html.twig", [
    	    'form' => $form->createView()
    	]);
    }
    
    public function updateAgendaAction(Request $request, Agenda $agenda) {
        $form = $this->createForm(AgendaUpdateType::class, $agenda, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_calendar_agenda_update', ['id' => $agenda->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            
            if ($agenda->getVisibility() === "share") {
                if ($request->request->get('members') !== null) {
                    $membersId = explode(',', $request->request->get('members'));
                    foreach ($membersId as $memberId) {
                        $member = $em->getRepository("UserBundle:User")->find($memberId);
                        $agenda->addMember($member);
                    }
                }
            }
            
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => $agenda->getName()], 'messages'));
            return $this->redirectToRoute('admin_calendar_agenda_list', ['id' => $agenda->getId()]);
        }
        
    	return $this->render("AdminBundle:Calendar:update_agenda.html.twig", array(
    		'agenda' => $agenda,
    	    'form' => $form->createView()
    	));
    }
    
    public function deleteAgendaAction(Request $request, Agenda $agenda) {
        $message = $this->get('translator')->trans('success.delete', ['%item%' => $agenda->getName()], 'messages');
        
    	$em = $this->getDoctrine()->getManager();
    	$em->remove($agenda);
    	$em->flush();
    
    	if ($request->isXmlHttpRequest() === true) {
    	    return new JsonResponse($message);
    	}
    	
    	$this->addFlash('success', $message);
    	return $this->redirect($this->generateUrl('admin_calendar_agenda_list'));
    }
    
    public function listMomentsAction(Request $request) {
        $moments = $this->getDoctrine()->getRepository(Moment::class)->findAll();
        if ($request->getMethod() == "POST") {
            $data = [];
            foreach ($moments as $moment) {
                $data[] = [
                    'id' => $moment->getId(),
                    'title' => $moment->getTitle(),
                    'start' => $moment->getStartedAt()->format("Y-m-d H:i"),
                    'end' => $moment->getEndedAt()->format("Y-m-d H:i"),
                    'color' => $moment->getColor(),
                    'url' => $this->generateUrl('admin_calendar_moment_update', array('id' => $moment->getId()))
                ];
            }
            
            return new JsonResponse($data);
        }
        
        return $this->render("AdminBundle:Calendar:list_moments.html.twig", array(
            'moments' => $moments,
            'mode' => $request->get('mode')
        ));
    }
    
    public function showMomentAction (Request $request, Moment $moment) {
        $em = $this->getDoctrine()->getManager();
        
        return $this->render("AdminBundle:Calendar:show_moment.html.twig", array(
            'moment' => $moment,
            'recurrence' => $em->getRepository(Recurrence::class)->findOneBy(NotificationUtil::constructTargetCriteria($moment)),
            'notification' => $em->getRepository(Notification::class)->findOneBy(NotificationUtil::constructTargetCriteria($moment)),
        ));
    }
    
    public function createMomentAction (Request $request) {
        $moment = new Moment();
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(MomentCreateType::class, $moment, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_calendar_moment_create'),
            'user'   => $this->getUser()
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_calendar_moment_create'];
            
            $moment->setStartedAt(new \DateTime($data['startedAt']));
            $moment->setEndedAt(new \DateTime($data['endedAt']));
            $moment->setTags($moment->getTags() !== null ? explode(',', $moment->getTags()) : null);
            $moment->setEnableComments($moment->getEnableComments() == "on" ? true : false);
            $moment->setIsRecurrent($moment->isRecurrent() == "on" ? true : false);
            
            if ($moment->getAgenda() === null){
                $agenda = new Agenda();
                $agenda->setName(Agenda::DEFAULT_NAME);
                $agenda->setVisibility(Agenda::VISIBILITY_PRIVATE);
                $moment->setAgenda($agenda);
                
                $em->persist($agenda);
            }
            
            if ($moment->getVisibility() === "share") {
                if ($request->request->get('members') !== null) {
                    $membersId = explode(',', $request->request->get('members'));
                    foreach ($membersId as $memberId) {
                        $member = $em->getRepository(User::class)->find($memberId);
                        $moment->addMember($member);
                    }
                }else {
                    $moment->setMembers($moment->getAgenda()->getMembers());
                }
            }
            
            $em->persist($moment);
            $em->flush();
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Moment::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($moment) {
                        $moment->setPicture($filename);
                    }
                ]));
            }
            
            if ($moment->isRecurrent() === true) {
                $eventData = NotificationUtil::mergeDataForScheduling($data, $moment, ApplyRecurrenceCommand::NAME, [$moment->getId()]);
                $eventData = array_merge($eventData, [
                    'recurrenceNextRunAt' => $moment->getEndedAt(),
                    'recurrenceIntervale' => $data['recurrenceIntervale'] ?? 1,
                    'recurrenceUnity' => $data['recurrenceUnity'] ?? Notification::UNITY_DAY,
                    'recurrenceExcludedDays' => $data['recurrenceExcludedDays'] ?? null,
                    'recurrenceDueAt' => isset($data['recurrenceDueAt']) ? new \DateTime($data['recurrenceDueAt']) : null
                ]);
                
                $this->get('event_dispatcher')->dispatch(SchedulingEvents::SCHEDULE, new SchedulingEvent($eventData));
            }
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => $moment->getTitle()], 'messages'));
            return $this->redirectToRoute('admin_calendar_moment_update', ['id' => $moment->getId()]);
        }
        
        return $this->render("AdminBundle:Calendar:create_moment.html.twig", array(
            'form' => $form->createView(),
            'mode' => $request->query->get('mode')
        ));
    }
    
    public function updateMomentAction (Request $request, Moment $moment) {
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(MomentUpdateType::class, $moment, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_calendar_moment_update', ['id' => $moment->getId()]),
            'user'   => $this->getUser()
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_calendar_moment_update'];
            
            $moment->setStartedAt(new \DateTime($data['startedAt']));
            $moment->setEndedAt(new \DateTime($data['endedAt']));
            $moment->setTags($moment->getTags() !== null ? explode(',', $moment->getTags()) : null);
            $moment->setEnableComments($moment->getEnableComments() == "on" ? true : false);
            $moment->setIsRecurrent($moment->isRecurrent() == "on" ? true : false);
            
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
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($moment->getPicture() === null || $moment->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Moment::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($moment) {
                        $moment->setPicture($filename);
                    }
                    ]));
            }
            
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => $moment->getTitle()], 'messages'));
            return $this->redirectToRoute('admin_calendar_moment_update', ['id' => $moment->getId()]);
        }
        
        return $this->render("AdminBundle:Calendar:update_moment.html.twig", array(
            'moment' => $moment,
            'form' => $form->createView(),
            'recurrence' => $em->getRepository(Recurrence::class)->findOneBy(NotificationUtil::constructTargetCriteria($moment)),
            'notification' => $em->getRepository(Notification::class)->findOneBy(NotificationUtil::constructTargetCriteria($moment)),
        ));
    }
    
    public function deleteMomentAction (Request $request, Moment $moment) {
        
        if ($moment->isRecurrent() === true) {
            $eventData = NotificationUtil::constructTargetCriteria($moment);
            $this->get('event_dispatcher')->dispatch(SchedulingEvents::UNSCHEDULE, new SchedulingEvent($eventData));
        }
        
        $message = $this->get('translator')->trans('success.delete', ['%item%' => $moment->getTitle()], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($moment);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_calendar_moment_list', array('mode' => $request->get('mode')));
    }
}
