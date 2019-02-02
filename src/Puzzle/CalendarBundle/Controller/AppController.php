<?php
namespace Puzzle\CalendarBundle\Controller;

use Puzzle\CalendarBundle\Entity\Agenda;
use Puzzle\SchedulingBundle\Util\NotificationUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\CalendarBundle\Entity\Moment;
use Puzzle\SchedulingBundle\Entity\Recurrence;
use Puzzle\SchedulingBundle\Entity\Notification;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class AppController extends Controller
{
    public function listAgendasAction(Request $request) {
        return $this->render("AppBundle:Calendar:list_agendas.html.twig", array(
            'agendas' => $this->getDoctrine()->getRepository(Agenda::class)->findAll()
        ));
    }
    
    public function showAgendaAction(Request $request, $id) {
        /** @var Doctrine\ORM\EntityManager **/
        $em = $this->get('doctrine.orm.entity_manager');
        
        if (! $agenda = $em->find(Agenda::class, $id)) {
            $agenda = $em->getRepository(Agenda::class)->findOneBy(['slug' => $id]);
        }
        
    	return $this->render("AppBundle:Calendar:show_agenda.html.twig", array(
    	    'agenda' => $agenda
    	));
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
                    'url' => $this->generateUrl('app_calendar_moment_show', array('id' => $moment->getId()))
                ];
            }
            
            return new JsonResponse($data);
        }
        
        return $this->render("AppBundle:Calendar:list_moments.html.twig", array(
            'moments' => $moments,
            'mode' => $request->get('mode')
        ));
    }
    
    public function showMomentAction (Request $request, $id) {
        
        /** @var Doctrine\ORM\EntityManager **/
        $em = $this->get('doctrine.orm.entity_manager');
        
        if (! $moment = $em->find(Agenda::class, $id)) {
            $moment = $em->getRepository(Moment::class)->findOneBy(['slug' => $id]);
        }
        
        $recurrence = null;
        if ($moment) {
            $recurrence = $em->getRepository(Recurrence::class)->findOneBy(NotificationUtil::constructTargetCriteria($moment));
        }
        
        $notification = null;
        if ($moment) {
            $notification = $em->getRepository(Notification::class)->findOneBy(NotificationUtil::constructTargetCriteria($moment));
        }
        
        return $this->render("AppBundle:Calendar:show_moment.html.twig", array(
            'moment' => $moment,
            'recurrence' => $recurrence,
            'notification' => $notification,
        ));
    }
}
