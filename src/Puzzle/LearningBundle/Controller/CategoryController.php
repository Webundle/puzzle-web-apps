<?php
namespace Puzzle\LearningBundle\Controller;

use Puzzle\LearningBundle\Entity\Category;
use Puzzle\LearningBundle\Form\Type\CategoryCreateType;
use Puzzle\LearningBundle\Form\Type\CategoryUpdateType;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\AdminBundle\Service\Validator;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class CategoryController extends Controller
{
    /***
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request) {
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_LEARNING", Validator::ACCESS);
            
            return $this->render("LearningBundle:Category:list.html.twig", array(
                'categories' => $this->getDoctrine()->getRepository(Category::class)->findAll()
            ));
        }catch (AccessDeniedHttpException $e){
            if ($request->isXmlHttpRequest() === true){
                return new JsonResponse($e->getMessage(), 403);
            }
            
            throw new AccessDeniedHttpException($e->getMessage());
        }
    }
    
    
    /***
     * Show category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Category $category) {
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_LEARNING", Validator::ACCESS);
            
            return $this->render("LearningBundle:Category:show.html.twig", array(
                'category' => $category
            ));
        }catch (AccessDeniedHttpException $e){
            if ($request->isXmlHttpRequest() === true){
                return new JsonResponse($e->getMessage(), 403);
            }
            
            throw new AccessDeniedHttpException($e->getMessage());
        }
    }
    
    
    /***
     * Create category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request) {
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_LEARNING", Validator::ACCESS);
            
            
            $category = new Category();
            $em = $this->getDoctrine()->getManager();
            $parentId = $request->query->get('parent');
            
            if ($parentId === true && $parent = $em->getRepository(Category::class)->find($parentId)){
                $category->setParent($parent);
            }
            
            $form = $this->createForm(CategoryCreateType::class, $category, [
                'method' => 'POST',
                'action' => $this->generateUrl('admin_learning_category_create')
            ]);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() === true && $form->isValid() === true) {
                $data = $request->request->all()['admin_learning_category_create'];
                $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
                
                if ($picture !== null) {
                    $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                        'path' => $picture,
                        'context' => MediaUtil::extractContext(Category::class),
                        'user' => $this->getUser(),
                        'closure' => function($filename) use ($category) {$category->setPicture($filename);}
                    ]));
                }
                
                $em->persist($category);
                $em->flush();
                
                if ($request->isXmlHttpRequest() === true) {
                    return new JsonResponse(['status' => true]);
                }
                
                $this->addFlash('success', $this->get('translator')->trans('success.post', [], 'messages'));
                return $this->redirectToRoute('admin_learning_category_update', ['id' => $category->getId()]);
            }
            
            return $this->render("LearningBundle:Category:create.html.twig", array(
                'form' => $form->createView(),
                'parent' => $request->query->get('parent')
            ));
        }catch (AccessDeniedHttpException $e){
            if ($request->isXmlHttpRequest() === true){
                return new JsonResponse($e->getMessage(), 400);
            }
            
            throw new AccessDeniedHttpException($e->getMessage());
        }
    }
    
    
    /***
     * Update category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, Category $category) {
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_LEARNING", Validator::ACCESS);
            
            $form = $this->createForm(CategoryUpdateType::class, $category, [
                'method' => 'POST',
                'action' => $this->generateUrl('admin_learning_category_update', ['id' => $category->getId()])
            ]);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() === true && $form->isValid() === true) {
                $data = $request->request->all()['admin_learning_category_update'];
                $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
                
                if ($category->getPicture() === null || $category->getPicture() !== $picture) {
                    $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                        'path' => $picture,
                        'context' => MediaUtil::extractContext(Category::class),
                        'user' => $this->getUser(),
                        'closure' => function($filename) use ($category) {$category->setPicture($filename);}
                    ]));
                }
                
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                
                if ($request->isXmlHttpRequest() === true) {
                    return new JsonResponse(['status' => true]);
                }
                
                $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
                return $this->redirectToRoute('admin_learning_category_update', ['id' => $category->getId()]);
            }
            
            return $this->render("LearningBundle:Category:update.html.twig", array(
                'category' => $category,
                'form' => $form->createView()
            ));
        }catch (AccessDeniedHttpException $e){
            if ($request->isXmlHttpRequest() === true){
                return new JsonResponse($e->getMessage(), 400);
            }
            
            throw new AccessDeniedHttpException($e->getMessage());
        }
    }
    
    
    /***
     * Delete category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, Category $category) {
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_LEARNING", Validator::ACCESS);
            
            
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            return $this->redirectToRoute('admin_learning_category_list');
        }catch (AccessDeniedHttpException $e){
            if ($request->isXmlHttpRequest() === true){
                return new JsonResponse($e->getMessage(), 400);
            }
            
            throw new AccessDeniedHttpException($e->getMessage());
        }
    }
}
