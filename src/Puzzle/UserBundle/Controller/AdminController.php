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
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdminController extends Controller
{
    /***
     * List users
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
     * Create user
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
            $user->setCredentialsExpiresAt(new \DateTime($data['credentialsExpiresAt']));
            $user->setAccountExpiresAt(new \DateTime($data['accountExpiresAt']));
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            /** User $user */
            $this->get('event_dispatcher')->dispatch(UserEvents::USER_CREATING, new UserEvent($user, [
                'plainPassword' => $data['plainPassword']['first']
            ]));
            
            if ($this->getParameter('user.registration.confirmation_link') === true) {
                /** User $user */
                $this->get('event_dispatcher')->dispatch(UserEvents::USER_CREATED, new UserEvent($user, [
                    'plainPassword' => $data['plainPassword']['first'],
                    'confirmationUrl' => $this->generateUrl('security_user_confirm_registration', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL)
                ]));
            }
            
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => $user->getFullName()], 'messages'));
            return $this->redirectToRoute('admin_user_update', ['id' => $user->getId()]);
        }
        
        return $this->render("AdminBundle:User:create_user.html.twig", [
            'form' => $form->createView(),
            'modules' => $this->getParameter('admin')['modules_available']
        ]);
    }
    
    /***
     * Update user
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateUserAction(Request $request, $id) {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        /** @var User $user */
        $user = $em->find(User::class, $id);
        
        $form = $this->createForm(UserUpdateType::class, $user, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_user_update', ['id' => $user->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_user_update'];
            
            // Update roles
            $roles = $request->request->get('roles') !== "" ? $request->request->get('roles') : $data['roles'];
            $user->setRoles(explode(',', $roles));
            
            // Update Security account
            if (isset($data['credentialsExpiresAt']) && $data['credentialsExpiresAt']) {
                $user->setCredentialsExpiresAt(new \DateTime($data['credentialsExpiresAt']));
            }
            
            if (isset($data['accountExpiresAt']) && $data['accountExpiresAt']) {
                $user->setAccountExpiresAt(new \DateTime($data['accountExpiresAt']));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            // Update password
            if (isset($data['plainPassword']['first']) === true && $data['plainPassword']['first'] !== "") {
                /** User $user */
                $this->get('event_dispatcher')->dispatch(UserEvents::USER_UPDATING, new UserEvent($user, [
                    'plainPassword' => $data['plainPassword']['first']
                ]));
            }
            
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => $user->getFullName()], 'messages'));
            return $this->redirectToRoute('admin_user_update', ['id' => $id]);
        }
        
        return $this->render("AdminBundle:User:update_user.html.twig", [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
    
    public function enableUserAction(Request $request, $id) {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        /** @var User $user */
        $user = $em->find(User::class, $id);
        $user->setEnabled(true);
        
        $em->flush();
        
        /** User $user */
        $this->get('event_dispatcher')->dispatch(UserEvents::USER_ENABLED, new UserEvent($user, [
            'confirmationUrl' => $this->generateUrl('security_user_confirm_registration', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL)
        ]));
        
        $message = $this->get('translator')->trans('user.registration.email.notification', [
            '%email%' => $user->getEmail()
        ], 'messages');
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_user_update', ['id' => $id]);
    }
    
    /**
     * Delete a user
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUserAction(Request $request, User $id) {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        /** @var User $user */
        $user = $em->find(User::class, $id);
        
        $message = $this->get('translator')->trans('success.delete', ['%item%' => (string) $user], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
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
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => $group->getName()], 'messages'));
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
    public function updateGroupAction(Request $request, $id){
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        /** @var User $user */
        $group = $em->find(Group::class, $id);
        
        $form = $this->createForm(GroupUpdateType::class, $group, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_user_group_update', ['id' => $group->getId()])
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => $group->getName()], 'messages'));
            return $this->redirectToRoute('admin_user_group_list', ['id' => $group->getId()]);
        }
        
        return $this->render("AdminBundle:User:update_group.html.twig", [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }
    
    
    /**
     * Delete a group
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteGroupAction(Request $request, $id) {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        /** @var User $user */
        $group = $em->find(Group::class, $id);
        
        $message = $this->get('translator')->trans('success.delete', ['%item%' => $group->getName()], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($group);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_user_group_list');
    }
}
