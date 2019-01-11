<?php
namespace Puzzle\CalendarBundle\Controller;

use Puzzle\CalendarBundle\Entity\Agenda;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Puzzle\UserBundle\Entity\User;
use Puzzle\CalendarBundle\Form\Type\AgendaCreateType;
use Puzzle\CalendarBundle\Form\Type\AgendaUpdateType;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class AgendaController extends Controller
{
    /***
     * Show agenda list
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAgendasAction(Request $request) {
        return $this->render("AdminBundle:Calendar:list_agenda.html.twig", array(
            'agendas' => $this->getDoctrine()->getRepository(Agenda::class)->findAll()
        ));
    }
    
    
    /***
     * Show an agenda
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAgendaAction(Request $request, Agenda $agenda) {
    	return $this->render("AdminBundle:Calendar:show_agenda.html.twig", array(
    	    'agenda' => $agenda
    	));
    }
    

    /***
     * Create a new agenda
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
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
            
            return $this->redirectToRoute('admin_calendar_agenda_list');
        }
        
    	return $this->render("CalendarBundle:Agenda:create.html.twig", [
    	    'form' => $form->createView()
    	]);
    }
    
    
    /***
     * Update an agenda
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, Agenda $agenda) {
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
            
            return $this->redirectToRoute('admin_calendar_agenda', ['id' => $agenda->getId()]);
        }
        
    	return $this->render("CalendarBundle:Agenda:update.html.twig", array(
    		'agenda' => $agenda,
    	    'form' => $form->createView()
    	));
    }
    
    
    /***
     * Remove an agenda
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAgendaAction(Request $request, Agenda $agenda) {
    	$em = $this->getDoctrine()->getManager();
    	$em->remove($agenda);
    	$em->flush();
    
    	if ($request->isXmlHttpRequest() === true) {
    	    return new JsonResponse(['status' => true]);
    	}
    	
    	return $this->redirect($this->generateUrl('admin_calendar_agenda_list'));
    }
}
