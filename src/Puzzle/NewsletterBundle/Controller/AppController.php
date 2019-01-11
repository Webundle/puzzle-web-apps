<?php

namespace Puzzle\NewsletterBundle\Controller;

use Puzzle\NewsletterBundle\NewsletterEvents;
use Puzzle\NewsletterBundle\Entity\Subscriber;
use Puzzle\NewsletterBundle\Event\SubscriberEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AppController extends Controller
{
    /**
     * create Subscriber
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createSubscriberAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        
        if (! $subscriber = $em->getRepository(Subscriber::class)->findOneBy(['email' => $data['email']])) {
            $subscriber = new Subscriber();
            $subscriber->setEmail($data['email']);
            
            if (isset($data['name']) === true) {
                $subscriber->setName($data['name']);
            }
            
            $em->persist($subscriber);
            $em->flush();
            
            $event = new SubscriberEvent($subscriber, [
                'subject' => $this->get('translator')->trans('newsletter.subscriber.mailing.welcome.subject', [
                    '%name%' => $data['name']
                ], 'messages'),
                'body' => $this->get('translator')->trans('newsletter.subscriber.mailing.welcome.body', [], 'messages')
            ]);
            $this->get('event_dispatcher')->dispatch(NewsletterEvents::NEW_SUBSCRIBER, $event);
            
            return new JsonResponse($this->get('translator')->trans('newsletter.subscriber.mailing.suscription_confirmed', [], 'messages'));
        }
        
        return new JsonResponse($this->get('translator')->trans('newsletter.subscriber.mailing.suscription_duplicated', [], 'messages'), 409);
    }
}
