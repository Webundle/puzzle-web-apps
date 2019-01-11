<?php

namespace Puzzle\AdminBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Puzzle\AdminBundle\Entity\Menu;
use Puzzle\AdminBundle\Entity\MenuItem;
use Puzzle\AdminBundle\Form\Type\MenuCreateType;
use Puzzle\AdminBundle\Form\Type\MenuItemType;
use Puzzle\AdminBundle\Form\Type\MenuUpdateType;
use Puzzle\AdminBundle\Service\Validator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MenuController extends Controller
{
    /***
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listMenusAction(Request $request){
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_ADMIN", Validator::ACCESS);
            return $this->render("AdminBundle:Menu:list.html.twig", array(
                'menus' => $this->getDoctrine()->getRepository(Menu::class)->findAll()
            ));
        }catch (AccessDeniedHttpException $e){
            if ($request->isXmlHttpRequest() === true){
                return new JsonResponse($e->getMessage(), 403);
            }
            
            throw new AccessDeniedHttpException($e->getMessage());
        }
    }
    
    
    /***
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showMenuAction(Request $request, Menu $menu){
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_ADMIN", Validator::ACCESS);
            
            return $this->render("AdminBundle:Menu:show.html.twig", array(
                'menu'      => $menu,
                'menuItems' => $menu->getMenuItems()
            ));
        }catch (AccessDeniedHttpException $e){
            if ($request->isXmlHttpRequest() === true){
                return new JsonResponse($e->getMessage(), 403);
            }
            
            throw new AccessDeniedHttpException($e->getMessage());
        }
    }
    
    
    /***
     * Create menu
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createMenuAction(Request $request){
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_ADMIN", Validator::MANAGE);
            
            $menu = new Menu();
            $form = $this->createForm(MenuCreateType::class, $menu, [
                'method' => 'POST',
                'action' => $this->generateUrl('admin_menu_create')
            ]);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() === true && $form->isValid() === true) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($menu);
                $em->flush();
                
                if ($request->isXmlHttpRequest() === true) {
                    return new JsonResponse(['status' => true]);
                }
                
                return $this->redirectToRoute('admin_menu_list');
            }
            
            return $this->render("AdminBundle:Menu:create.html.twig", array(
                'form' => $form->createView()
            ));
        }catch (BadRequestHttpException $e){
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($e->getMessage(), 400);
            }
            
            throw new BadRequestHttpException($e->getMessage());
        }
    }
    
    
    /***
     * Edit menu
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateMenuAction(Request $request, Menu $menu){
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_ADMIN", Validator::MANAGE);
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            
            $form = $this->createForm(MenuUpdateType::class, $menu, [
                'method' => 'POST',
                'action' => $this->generateUrl('admin_menu_update', ['id' => $menu->getId()])
            ]);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() === true && $form->isValid() === true) {
                $em->flush();
                if ($request->isXmlHttpRequest() === true) {
                    return new JsonResponse(['status' => true]);
                }
                
                return $this->redirectToRoute('admin_menu_show', ['id' => $menu->getId()]);
            }
            
            return $this->render("AdminBundle:Menu:update.html.twig", array(
                'menu' => $menu,
                'form' => $form->createView()
            ));
        }catch (BadRequestHttpException $e){
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($e->getMessage(), 400);
            }
            
            throw new BadRequestHttpException($e->getMessage());
        }
    }
    
    
    /***
     * Delete menu
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteMenuAction(Request $request, Menu $menu){
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_ADMIN", Validator::MANAGE);
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->remove($menu);
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            return $this->redirectToRoute('admin_menu_list');
        }catch (BadRequestHttpException $e){
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($e->getMessage(), 400);
            }
            
            throw new BadRequestHttpException($e->getMessage());
        }
    }
    
  
    /***
     * Create menu item
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createMenuItemAction(Request $request, Menu $menu){
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_ADMIN", Validator::MANAGE);
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            
            $menuItem = new MenuItem();
            $menuItem->setMenu($menu);
            
            $form = $this->createForm(MenuItemType::class, $menuItem, [
                'method' => 'POST',
                'action' => $this->generateUrl('admin_menu_item_create', ['id' => $menu->getId()])
            ]);
            $form->add('parent', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'admin.property.menuItem.parent',
                'label_attr' => ['class' => 'uk-form-label'],
                'class' => 'AdminBundle:MenuItem',
                'query_builder' => function (EntityRepository $er) use ($menu){
                return $er->createQueryBuilder('m')
                          ->where("m.menu = :menu")
                          ->setParameter(':menu', $menu->getId())
                          ->orderBy('m.name', 'ASC');
                },
                'choice_label' => 'name',
                'placeholder' => 'Pas de parent',
                'attr' => ['class' => 'md-input'],
                'required' => false
            ));
            $form->handleRequest($request);
            
            if ($form->isSubmitted() === true && $form->isValid() === true) {
                $em->persist($menuItem);
                $em->flush($menuItem);
                
                if ($request->isXmlHttpRequest() === true) {
                    return new JsonResponse(['status' => true]);
                }
                
                return $this->redirectToRoute('admin_menu_show', ['id' => $menu->getId()]);
            }
            
            return $this->render("AdminBundle:MenuItem:create.html.twig", array(
                'menu' => $menu,
                'form' => $form->createView(),
            ));
        }catch (BadRequestHttpException $e){
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($e->getMessage(), 400);
            }
            
            throw new BadRequestHttpException($e->getMessage());
        }
    }
    
    
    /***
     * Update menu
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateMenuItemAction(Request $request, Menu $menu, $itemId){
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_ADMIN", Validator::MANAGE);
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $menuItem = $em->getRepository(MenuItem::class)->find($itemId);
            
            $form = $this->createForm(MenuItemType::class, $menuItem, [
                'method' => 'POST',
                'action' => $this->generateUrl('admin_menu_item_update', ['id' => $menu->getId(), 'itemId' => $itemId])
            ]);
            $form->add('parent', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'admin.property.menuItem.parent',
                'label_attr' => ['class' => 'uk-form-label'],
                'class' => 'AdminBundle:MenuItem',
                'query_builder' => function (EntityRepository $er) use ($menu, $itemId){
                return $er->createQueryBuilder('m')
                          ->where("m.menu = :menu")
                          ->andWhere("m.id != :id")
                          ->setParameter(':menu', $menu->getId())
                          ->setParameter(':id', $itemId)
                          ->orderBy('m.name', 'ASC');
                },
                'choice_label' => 'name',
                'placeholder' => 'Pas de parent',
                'attr' => ['class' => 'md-input'],
                'required' => false
                ));
            $form->handleRequest($request);
            
            if ($form->isSubmitted() === true && $form->isValid() === true) {
                $em->flush();
                
                if ($request->isXmlHttpRequest() === true) {
                    return new JsonResponse(['status' => true]);
                }
                
                return $this->redirectToRoute('admin_menu_show', ['id' => $menu->getId()]);
            }
            
            return $this->render("AdminBundle:MenuItem:update.html.twig", array(
                'menu' => $menu,
                'menuItem' => $menuItem,
                'form' => $form->createView()
            ));
        }catch (BadRequestHttpException $e){
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($e->getMessage(), 400);
            }
            
            throw new BadRequestHttpException($e->getMessage());
        }
    }
    
    
    /***
     * Delete menu item
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteMenuItemAction(Request $request, Menu $menu, $itemId){
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_ADMIN", Validator::MANAGE);
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $menuItem = $em->getRepository(MenuItem::class)->find($itemId);
            $em->remove($menuItem);
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            return $this->redirectToRoute('admin_menu_show', ['id' => $menu->getId()]);
        }catch (BadRequestHttpException $e){
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($e->getMessage(), 400);
            }
            
            throw new BadRequestHttpException($e->getMessage());
        }
        
    }
}
