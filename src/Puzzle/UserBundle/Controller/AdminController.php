<?php

namespace Puzzle\UserBundle\Controller;

use Puzzle\UserBundle\Entity\User;
use Puzzle\UserBundle\Entity\Group;
use Puzzle\UserBundle\Form\Type\UserCreateType;
use Puzzle\UserBundle\Form\Type\UserUpdateType;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\UserBundle\Form\Type\GroupCreateType;
use Puzzle\UserBundle\Form\Type\GroupUpdateType;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\UserBundle\UserEvents;
use Puzzle\UserBundle\Event\UserEvent;

class AdminController extends Controller
{
    /***
     * Show Users
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listUsersAction(Request $request) {
    	return $this->render("AdminBundle:User:list_users.html.twig", array(
    	    'users' => $this->getDoctrine()->getRepository(User::class)->findAll()
    	));
    }
    
    /***
     * Create User
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createUserAction(Request $request){
        $user = new User();
        $form = $this->createForm(UserCreateType::class, $user, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_user_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_user_create'];
            
            // Update roles
            $roles = $request->request->get('roles') !== "" ? $request->request->get('roles') : $data['roles'];
            $user->setRoles(explode(',', $roles));
            
            // Update Security account
            $user->setCredentialsExpiresAt(new \DateTime($data['credentialsExpiredAt']));
            $user->setAccountExpiresAt(new \DateTime($data['credentialsExpiredAt']));
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            // Update password
            if (isset($data['plainPassword']['first']) === true && $data['plainPassword']['first'] !== "") {
                /** User $user */
                $this->get('event_dispatcher')->dispatch(UserEvents::USER_PASSWORD, new UserEvent($user, [
                    'plainPassword' => $data['plainPassword']['first']
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(User::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($user) {
                        $user->setPicture($filename);
                    }
                ]));
            }
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', [], 'messages'));
            return $this->redirectToRoute('admin_user_update', ['id' => $user->getId()]);
        }
        
        return $this->render("AdminBundle:User:create_user.html.twig", [
            'form' => $form->createView(),
            'modules' => $this->getParameter('admin')['modules_available']
        ]);
    }
    
    /***
     * Update User
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateUserAction(Request $request, User $user){
        $form = $this->createForm(UserUpdateType::class, $user, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_user_update', ['id' => $user->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_user_create'];
            
            // Update roles
            $roles = $request->request->get('roles') !== "" ? $request->request->get('roles') : $data['roles'];
            $user->setRoles(explode(',', $roles));
            
            // Update Security account
            $user->setCredentialsExpiresAt(new \DateTime($data['credentialsExpiredAt']));
            $user->setAccountExpiresAt(new \DateTime($data['credentialsExpiredAt']));
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            // Update password
            if (isset($data['plainPassword']['first']) === true && $data['plainPassword']['first'] !== "") {
                /** User $user */
                $this->get('event_dispatcher')->dispatch(UserEvents::USER_PASSWORD, new UserEvent($user, [
                    'plainPassword' => $data['plainPassword']['first']
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            if ($user->getPicture() === null || $user->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(User::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($user) {
                        $user->setPicture($filename);
                    }
                 ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
            return $this->redirectToRoute('admin_user_update', ['id' => $user->getId()]);
        }
        
        return $this->render("AdminBundle:User:update_user.html.twig", [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * Delete a user
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUserAction(Request $request, User $user) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(['status' => true]);
        }
        
        $this->addFlash('success', $this->get('translator')->trans('success.delete', [], 'messages'));
        return $this->redirectToRoute('admin_user_list');
    }
    
    /***
     * Show groups
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listGroupsAction(Request $request) {
        return $this->render("AdminBundle:User:list_groups.html.twig", array(
            'groups' => $this->getDoctrine()->getRepository(Group::class)->findBy([], ['name' => 'ASC']),
        ));
    }
    
    /***
     * Create group
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createGroupAction(Request $request){
        $group = new Group();
        $form = $this->createForm(GroupCreateType::class, $group, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_user_group_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', [], 'messages'));
            return $this->redirectToRoute('admin_user_group_update', ['id' => $group->getId()]);
        }
        
        return $this->render("AdminBundle:User:create_group.html.twig", [
            'form' => $form->createView(),
        ]);
    }
    
    /***
     * Update group
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateGroupAction(Request $request, Group $group){
        $form = $this->createForm(GroupUpdateType::class, $group, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_user_group_update', ['id' => $group->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
            return $this->redirectToRoute('admin_user_group_update', ['id' => $group->getId()]);
        }
        
        return $this->render("UserBundle:Group:update_group.html.twig", [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }
    
    
    /**
     * Delete a group
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteGroupAction(Request $request, Group $group) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($group);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(['status' => true]);
        }
        
        $this->addFlash('success', $this->get('translator')->trans('success.delete', [], 'messages'));
        return $this->redirectToRoute('admin_user_group_list');
    }
}
