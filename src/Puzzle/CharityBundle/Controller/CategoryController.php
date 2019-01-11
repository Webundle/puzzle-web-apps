<?php
namespace Puzzle\CharityBundle\Controller;

use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\CharityBundle\Form\Type\CategoryCreateType;
use Puzzle\CharityBundle\Form\Type\CategoryUpdateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Puzzle\CharityBundle\Entity\Category;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class CategoryController extends Controller
{
    /***
     * Show categories
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_CHARITY') or has_role('ROLE_ADMIN')")
     */
    public function listAction(Request $request) {
        return $this->render("CharityBundle:Category:list.html.twig", array(
            'categories' => $this->getDoctrine()->getRepository(Category::class)->findAll()
        ));
    }
    
    /***
     * Create category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_CHARITY') or has_role('ROLE_ADMIN')")
     */
    public function createAction(Request $request) {
        $category = new Category();
        $em = $this->getDoctrine()->getManager();
        $parentId = $request->query->get('parent');
        
        if ($parentId === true && $parent = $em->getRepository(Category::class)->find($parentId)){
            $category->setParentNode($parent);
        }
        
        $form = $this->createForm(CategoryCreateType::class, $category, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_charity_category_create')
        ]);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_charity_category_create'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Category::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($category) {
                        $category->setPicture($filename);
                    }
                 ]));
            }
            
            $em->persist($category);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', [], 'messages'));
            return $this->redirectToRoute('admin_charity_category_update', ['id' => $category->getId()]);
        }
            
        return $this->render("CharityBundle:Category:create.html.twig", array(
            'form' => $form->createView(),
            'parent' => $request->query->get('parent')
        ));
    }
    
    
    /***
     * Update category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_CHARITY') or has_role('ROLE_ADMIN')")
     */
    public function updateAction(Request $request, Category $category) {
        $form = $this->createForm(CategoryUpdateType::class, $category, [
            'method' => 'POST', 
            'action' => $this->generateUrl('admin_charity_category_update', ['id' => $category->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_charity_category_update'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($category->getPicture() === null || $category->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Category::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($category) {
                        $category->setPicture($filename);
                    }
                 ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
            return $this->redirectToRoute('admin_charity_category_update', ['id' => $category->getId()]);
        }
        
        return $this->render("CharityBundle:Category:update.html.twig", array(
            'category' => $category,
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Delete category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_CHARITY') or has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        
        $this->addFlash('success', $this->get('translator')->trans('success.delete', [], 'messages'));
        return $this->redirectToRoute('admin_charity_category_list');
    }
}
