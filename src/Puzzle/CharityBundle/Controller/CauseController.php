<?php
namespace Puzzle\CharityBundle\Controller;

use Puzzle\CharityBundle\Entity\Cause;
use Puzzle\CharityBundle\Form\Type\CauseCreateType;
use Puzzle\CharityBundle\Form\Type\CauseUpdateType;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class CauseController extends Controller
{
    /***
     * Show Causes
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_CHARITY') or has_role('ROLE_ADMIN')")
     */
    public function listAction(Request $request){
        return $this->render("CharityBundle:Cause:list.html.twig", array(
            'causes' => $this->getDoctrine()->getRepository(Cause::class)->findAll()
        ));
    }
    
    /***
     * Create cause
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_CHARITY') or has_role('ROLE_ADMIN')")
     */
    public function createAction(Request $request) {
        $cause = new Cause();
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(CauseCreateType::class, $cause, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_charity_cause_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_charity_cause_create'];
            
            $tags = $cause->getTags() !== null ? explode(',', $cause->getTags()) : null;
            $cause->setTags($tags);
            
            $cause->setStartedAt($data['startedAt']);
            $cause->setEndedAt($data['endedAt']);
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Cause::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($cause) {$cause->setPicture($filename);}
                 ]));
            }
            
            $em->persist($cause);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', [], 'messages'));
            return $this->redirectToRoute('admin_charity_cause_update', ['id' => $cause->getId()]);
        }
        
        return $this->render("CharityBundle:Cause:create.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Update cause
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_CHARITY') or has_role('ROLE_ADMIN')")
     */
    public function updateAction(Request $request, Cause $cause) {
        $form = $this->createForm(CauseUpdateType::class, $cause, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_charity_cause_update', ['id' => $cause->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_charity_cause_update'];
            
            $tags = $cause->getTags() !== null ? explode(',', $cause->getTags()) : null;
            $cause->setTags($tags);
            
            $cause->setStartedAt($data['startedAt']);
            $cause->setEndedAt($data['endedAt']);
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($cause->getPicture() === null || $cause->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Cause::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($cause) {$cause->setPicture($filename);}
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
            return $this->redirectToRoute('admin_charity_cause_update', ['id' => $cause->getId()]);
        }
        
        return $this->render("CharityBundle:Cause:update.html.twig", array(
            'cause' => $cause,
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Delete Cause
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_CHARITY') or has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, Cause $cause) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($cause);
        $em->flush();
        
        $this->addFlash('success', $this->get('translator')->trans('success.delete', [], 'messages'));
        return $this->redirectToRoute('admin_charity_cause_list');
    }
}
