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
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', [], 'messages'));
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
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
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
        $em = $this->getDoctrine()->getManager();
        $em->remove($page);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(['status' => true]);
        }
        
        $this->addFlash('success', $this->get('translator')->trans('success.delete', [], 'messages'));
        return $this->redirectToRoute('admin_static_page_list');
    }
}
