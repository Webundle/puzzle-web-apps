<?php
namespace Puzzle\MailBundle\Controller;

use Puzzle\MailBundle\Entity\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MailBundle\Form\Type\TemplateFormType;
use Puzzle\MediaBundle\Util\MediaUtil;

/**
 * @author Gnagne Cedric <cecenho55@gmail.com>
 */
class TemplateController extends Controller
{
    /***
     * Show Templates
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showListAction(Request $request){
        /** @var EntityRepository $er */
        $er = $this->getDoctrine()->getRepository("MailBundle:Template");
        
        if ($request->isMethod('POST') === true && $request->isXmlHttpRequest() === true) {
            $templates = $er->customFindBy(null, null, $criteria);
            $array = [];
            foreach ($templates as $template) {
                $array[] = [
                    'id' => $template->getId(),
                    'name' => $template->getName(),
                    'parent' => $template->getParent() !== null ? $template->getParent()->getName() : ""
                ];
            }
            
            return new JsonResponse($array);
        }
        
        return $this->render("MailBundle:Template:list.html.twig", array(
            'templates' => $er->customFindBy(null, null, [])
        ));
    }
    
    
    /***
     * Show Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Template $template){
        return $this->redirect($template->getDocument());
    }
    
    /***
     * Create template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request){
        $template = new Template();
        
        $form = $this->createForm(TemplateFormType::class, $template);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            
            if ($template->getDocument() !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $template->getDocument(),
                    'context' => MediaUtil::extractContext(Template::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($template) {
                        $template->setDocument($filename);
                    }
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($template);
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            return $this->redirectToRoute('admin_newsletter_templates');
        }
        
        return $this->render("MailBundle:Template:create.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Update template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, Template $template) {
        $form = $this->createForm(TemplateFormType::class, $template);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $template->setUser($this->getUser());
            
            if ($template->getDocument() !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $template->getDocument(),
                    'context' => MediaUtil::extractContext(Template::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($template) {
                        $template->setDocument($filename);
                    }
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            return $this->redirectToRoute('admin_newsletter_templates');
        }
        
        return $this->render("MailBundle:Template:update.html.twig", array(
            'template' => $template,
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Remove template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Request $request, Template $template) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($template);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(['status' => true]);
        }
        
        return $this->redirectToRoute('admin_newsletter_templates');
    }
    
    
    /***
     * Remove templates
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeListAction(Request $request){
        $list = explode(',', $request->request->get('ids'));
        $em = $this->getDoctrine()->getManager();
        
        foreach ($list as $id) {
            if ($id !== null) {
                $template = $em->getRepository("MailBundle:Template")->find($id);
                $em->remove($template);
            }
        }
        
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(['status' => true]);
        }
        
        return $this->redirectToRoute('admin_newsletter_templates');
    }
}
