<?php

namespace Puzzle\ContactBundle\Controller;

use Puzzle\ContactBundle\Entity\Contact;
use Puzzle\ContactBundle\Entity\Group;
use Puzzle\ContactBundle\Form\Type\ContactCreateType;
use Puzzle\ContactBundle\Form\Type\ContactUpdateType;
use Puzzle\MediaBundle\Entity\File;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\ContactBundle\Form\Type\GroupCreateType;
use Puzzle\ContactBundle\Form\Type\GroupUpdateType;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;

class AdminController extends Controller
{
    /***
     * Show Contacts
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listContactsAction(Request $request) {
    	return $this->render("AdminBundle:Contact:list_contacts.html.twig", array(
    	    'contacts' => $this->getDoctrine()->getRepository(Contact::class)->findAll()
    	));
    }
    
    /***
     * Create Contact
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createContactAction(Request $request){
        $contact = new Contact();
        $form = $this->createForm(ContactCreateType::class, $contact, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_contact_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            if ($user = $contact->getUser()) {
                $contact->setEmail($contact->getEmail() ?? $user->getEmail());
                $contact->setFirstName($contact->getFirstName() ?? $user->getFirstName());
                $contact->setLastName($contact->getLastName() ?? $user->getLastName());
                $contact->setPhoneNumber($contact->getPhoneNumber() ?? $user->getPhoneNumber());
            }
            
            $data = $request->request->all()['admin_contact_create'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Contact::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($contact) {
                        $contact->setPicture($filename);
                    }
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => (string) $contact], 'messages'));
            return $this->redirectToRoute('admin_contact_update', ['id' => $contact->getId()]);
        }
        
        return $this->render("AdminBundle:Contact:create_contact.html.twig", [
            'form' => $form->createView()
        ]);
    }
    
    /***
     * Update Contact
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateContactAction(Request $request, Contact $contact){
        $form = $this->createForm(ContactUpdateType::class, $contact, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_contact_update', ['id' => $contact->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_contact_update'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($contact->getPicture() === null || $contact->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Contact::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($contact) {
                        $contact->setPicture($filename);
                    }
                 ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => (string) $contact], 'messages'));
            return $this->redirectToRoute('admin_contact_update', ['id' => $contact->getId()]);
        }
        
        return $this->render("AdminBundle:Contact:update_contact.html.twig", [
            'contact' => $contact,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * Export contacts
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function exportContactAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$contacts = null;
    	$date = new \DateTime();
    	$basename = $date->getTimestamp().'.csv';
    	
    	if ($groupId =  $request->query->get('group')) {
    	    $group = $em->getRepository(Group::class)->find($groupId);
    	    
    	    if ($group !== null) {
    	        $basename = $group->getName().'.csv';
    	        if ($group->getContacts() !== null) {
    	            $contacts = $group->getContacts();
    	        }
    	    }
    	}else {
    	    $contacts = $this->getDoctrine()->getRepository(Contact::class)->findAll();
    	}
    	
    	$fs = new Filesystem();
    	$dirname = File::getBaseDir().File::getBasePath().'/contact/contacts';
    	
    	if(!$fs->exists($dirname)){
    	    $fs->mkdir($dirname);
    	}
    	
    	if ($contacts !== null ){
    	    $filename = $dirname.'/'.$basename;
    	    $fp = fopen($filename, 'w');
    	    fputcsv($fp, [
    	        $this->get('translator')->trans('contact.full_name', [], 'messages'),
    	        $this->get('translator')->trans('contact.email', [], 'messages'),
    	        $this->get('translator')->trans('contact.phone', [], 'messages'),
    	        $this->get('translator')->trans('contact.company', [], 'messages'),
    	        $this->get('translator')->trans('contact.position', [], 'messages'),
    	        $this->get('translator')->trans('contact.location', [], 'messages'),
    	        $this->get('translator')->trans('contact.picture', [], 'messages'),
    	    ]);
    	    foreach ($contacts as $contact){
    	        fputcsv($fp, [
    	            $contact->getFullName(),
    	            $contact->getEmail(),
    	            $contact->getPhone(),
    	            $contact->getCompany(),
    	            $contact->getPosition(),
    	            $contact->getLocation(),
    	            $contact->getPicture()
    	        ]);
    	    }
    	    
    	    fclose($fp);
    	}
    	
    	$route = File::getBasePath().'/contact/contacts/'.$basename;
    	
    	if ($request->isXmlHttpRequest() === true) {
    	    return new JsonResponse([
    	        'status' => true,
    	        'href' => $route
    	    ]);
    	}
    	
    	return $this->redirect($route);
    }
    
    /**
     * Import contacts
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function importAction(Request $request) {
        if ($request->isMethod("POST") === true) {
            $folder = $this->get('media.file_manager')->createFolder(MediaUtil::extractContext(Contact::class), $this->getUser(), true);
            $media = $this->get('media.upload_manager')->prepareUpload($_FILES, $folder, $this->getUser());
            
            $filename = $media[0]->getAbsolutePath();
            $fp = fopen($filename, 'r');
            
            $count = 0;
            
            $em = $this->getDoctrine()->getManager();
            $group = null;
            
            while (feof($fp) === false) {
                $row = fgetcsv($fp);
                
                if (is_array($row) && (int)$row[0] !== false) {
                    $row[5] = trim($row[5]);
                    $contact = $em->getRepository(Contact::class)->findOneBy(['email' => $row[5]]);
                    if ($row[5] === "" || $contact === null) {
                        $contact = new Contact();
                        $em->persist($contact);
                    }
                    
                    $contact->setFirstName(trim($row[1]));
                    $contact->setLastName(trim($row[2]));
                    $contact->setCompany(trim($row[4]));
                    $contact->setEmail(trim($row[5]));
                    $contact->setPhone(trim($row[6]));
                    $contact->setLocation(trim($row[7]));
                    
                    $em->flush();
                    
                    $group = $em->getRepository(Group::class)->findOneBy(array('name' => $row[3]));
                    if ($group === null) {
                        $group = new Group();
                        $group->setName($row[3]);
                        
                        $em->persist($group);
                    }
                    
                    $group->addContact($contact->getId());
                    
                    $em->flush();
                    $count++;
                }
            }
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => (string)$contact], 'messages'));
            return $this->redirectToRoute('admin_contact_list');
        }
        
        return $this->render("AdminBundle:Contact:import_contact.html.twig");
    }
    
    /**
     * Delete a contact
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteContactAction(Request $request, Contact $contact) {
        $message =  $this->get('translator')->trans('success.delete', ['%item%' => (string)$contact], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($contact);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_contact_list');
    }
    
    /***
     * Show groups
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listGroupsAction(Request $request) {
        return $this->render("AdminBundle:Contact:list_groups.html.twig", array(
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
            'action' => $this->generateUrl('admin_contact_group_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => $group->getName()], 'messages'));
            return $this->redirectToRoute('admin_contact_group_list');
        }
        
        return $this->render("AdminBundle:Contact:create_group.html.twig", [
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
            'action' => $this->generateUrl('admin_contact_group_update', ['id' => $group->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => $group->getName()], 'messages'));
            return $this->redirectToRoute('admin_contact_group_list');
        }
        
        return $this->render("AdminBundle:Contact:update_group.html.twig", [
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
        $message = $this->get('translator')->trans('success.delete', ['%item%' => $group->getName()], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($group);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_contact_group_list');
    }
    
    
    /***
     * Show requests
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listRequestsAction(Request $requestHttp) {
        return $this->render("AdminBundle:Contact:list_requests.html.twig", array(
            'requests' => $this->getDoctrine()->getRepository(\Puzzle\ContactBundle\Entity\Request::class)->findBy([], [
                'markAsRead' => 'ASC',
                'createdAt' => 'DESC',
            ]),
        ));
    }
    
    /***
     * Show request
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showRequestAction(Request $requestHttp, \Puzzle\ContactBundle\Entity\Request $request) {
        $request->markAsRead();
        return $this->render("AdminBundle:Contact:show_request.html.twig", array(
            'request' => $request
        ));
    }
    
    /**
     * Delete a request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteRequestAction(Request $requestHttp, \Puzzle\ContactBundle\Entity\Request $request) {
        $message = $this->get('translator')->trans('success.delete', ['%item%' => $request->getSubject()], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($request);
        $em->flush();
        
        if ($requestHttp->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_contact_request_list');
    }
}
