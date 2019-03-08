<?php

namespace Puzzle\ContactBundle\Controller;

use Puzzle\ContactBundle\Entity\Contact;
use Puzzle\ContactBundle\Entity\Group;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\ContactBundle\ContactEvents;
use Puzzle\ContactBundle\Event\ContactEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class AppController extends Controller
{
    /**
     * create contact
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createContactAction(Request $requestHttp) {
        $data = $requestHttp->request->all();
        $em = $this->getDoctrine()->getManager();
        
        if (! $contact = $em->getRepository(Contact::class)->findOneBy(['email' => $data['email']])) {
            $contact = new Contact(); 
            $em->persist($contact);
            
            if (isset($data['firstName']) && $data['firstName']) {
                $contact->setFirstName($data['firstName']);
            }
            
            if (isset($data['lastName']) && $data['lastName']) {
                $contact->setLastName($data['lastName']);
            }
            
            if (isset($data['name']) && $data['name']) {
                $names = explode(' ', $data['name']);
                $contact->setLastName($names[0]);
                $contact->setFirstName(trim(str_replace($names[0], '', $data['name'])));
            }
            
            if (isset($data['email']) && $data['email']) {
                $contact->setEmail($data['email']);
            }
            
            if (isset($data['phoneNumber']) && $data['phoneNumber']) {
                $contact->setPhoneNumber($data['phoneNumber']);
            }
            
            if (isset($data['company']) && $data['company']) {
                $contact->setCompany($data['company']);
            }
            
            if (isset($data['position']) && $data['position']) {
                $contact->setPosition($data['position']);
            }
            
            if (isset($data['location']) && $data['location']) {
                $contact->setLocation($data['location']);
            }
        }
        
        $em->flush();
        
        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispacth(ContactEvents::CREATE_CONTACT, new ContactEvent($contact, $data));
        
        if ($requestHttp->isXmlHttpRequest() === true){
            return new JsonResponse($this->get('translator')->trans('contact.request.success', [], 'messages'));
        }
        
        return $this->redirectToRoute('app_homepage');
    }
}
