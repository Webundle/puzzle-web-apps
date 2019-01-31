<?php
namespace Puzzle\StaticBundle\Controller;

use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Util\MediaUtil;
use Puzzle\StaticBundle\Entity\Page;
use Puzzle\StaticBundle\Form\Type\PageCreateType;
use Puzzle\StaticBundle\Form\Type\PageUpdateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\StaticBundle\Form\Type\TemplateUpdateType;
use Puzzle\StaticBundle\Form\Type\TemplateCreateType;
use Puzzle\StaticBundle\Entity\Template;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class AdminController extends Controller
{
    /***
     * List pages
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPagesAction(Request $request){
        return $this->render("AdminBundle:Static:list_pages.html.twig", array(
            'pages' => $this->getDoctrine()->getRepository(Page::class)->findBy([], ['createdAt' => 'DESC'])
        ));
    }
    
    /***
     * Create page
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createPageAction(Request $request) {
        $page = new Page();
        $form = $this->createForm(PageCreateType::class, $page, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_static_page_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            if ($page->getPicture() !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $page->getPicture(),
                    'context' => MediaUtil::extractContext(Page::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($page) {$page->setPicture($filename);}
                 ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => $page->getName()], 'messages'));
            return $this->redirectToRoute('admin_static_page_update', ['id' => $page->getId()]);
        }
        
        return $this->render("AdminBundle:Static:create_page.html.twig", array(
            'form' => $form->createView(),
            'parent' => $request->query->get('parent')
        ));
    }
    
    /***
     * Update page
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updatePageAction(Request $request, Page $page) {
        $form = $this->createForm(PageUpdateType::class, $page, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_static_page_update', ['id' => $page->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            if ($page->getPicture() !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $page->getPicture(),
                    'context' => MediaUtil::extractContext(Page::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($page) {$page->setPicture($filename);}
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => $page->getName()], 'messages'));
            return $this->redirectToRoute('admin_static_page_update', ['id' => $page->getId()]);
        }
        
        return $this->render("AdminBundle:Static:update_page.html.twig", array(
            'page' => $page,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete page
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletePageAction(Request $request, Page $page) {
        $message = $this->get('translator')->trans('success.delete', ['%item%' => $page->getName()], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($page);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_static_page_list');
    }
    
    
    /***
     * List templates
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listTemplatesAction(Request $request) {
        return $this->render("AdminBundle:Static:list_templates.html.twig", array(
            'templates' => $this->getDoctrine()->getRepository(Template::class)->findBy([], ['createdAt' => 'DESC'])
        ));
    }
    
    /***
     * Create template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createTemplateAction(Request $request) {
        $template = new Template();
        $form = $this->createForm(TemplateCreateType::class, $template, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_static_template_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($template);
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => $template->getName()], 'messages'));
            return $this->redirectToRoute('admin_static_template_update', ['id' => $template->getId()]);
        }
        
        return $this->render("AdminBundle:Static:create_template.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    /***
     * Update template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateTemplateAction(Request $request, Template $template) {
        $form = $this->createForm(TemplateUpdateType::class, $template, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_static_template_update', ['id' => $template->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => $template->getName()], 'messages'));
            return $this->redirectToRoute('admin_static_template_update', ['id' => $template->getId()]);
        }
        
        return $this->render("AdminBundle:Static:update_template.html.twig", array(
            'template' => $template,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteTemplateAction(Request $request, Template $template) {
        $message = $this->get('translator')->trans('success.delete', ['%item%' => $template->getName()], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($template);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_static_template_list');
    }
}
