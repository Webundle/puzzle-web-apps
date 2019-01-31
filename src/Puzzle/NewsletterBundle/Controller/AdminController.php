<?php
namespace Puzzle\NewsletterBundle\Controller;

use Puzzle\NewsletterBundle\Entity\Group;
use Puzzle\NewsletterBundle\Form\Type\GroupCreateType;
use Puzzle\NewsletterBundle\Form\Type\GroupUpdateType;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\NewsletterBundle\Entity\Subscriber;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Puzzle\NewsletterBundle\Form\Type\SubscriberUpdateType;
use Puzzle\NewsletterBundle\Form\Type\SubscriberCreateType;
use Puzzle\NewsletterBundle\Entity\Template;
use Puzzle\NewsletterBundle\Form\Type\TemplateUpdateType;
use Puzzle\NewsletterBundle\Form\Type\TemplateCreateType;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class AdminController extends Controller
{
    /***
     * Show groups
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listGroupsAction(Request $request) {
        return $this->render("AdminBundle:Newsletter:list_groups.html.twig", array(
            'groups' => $this->getDoctrine()->getRepository(Group::class)->findBy([], ['createdAt' => 'DESC'])
        ));
    }
    
    /***
     * Show group
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showGroupAction(Request $request, Group $group) {
        return $this->render("AdminBundle:Newsletter:show_group.html.twig", array(
            'group' => $group
        ));
    }
    
    /***
     * Create group
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createGroupAction(Request $request) {
        $group = new Group();
        $em = $this->getDoctrine()->getManager();
        $parentId = $request->query->get('parent');
        
        if ($parentId === true && $parent = $em->getRepository(Group::class)->find($parentId)){
            $group->setParentNode($parent);
        }
        
        $form = $this->createForm(GroupCreateType::class, $group, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_newsletter_group_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em->persist($group);
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => $group->getName()], 'messages'));
            return $this->redirectToRoute('admin_newsletter_group_list');
        }
        
        return $this->render("AdminBundle:Newsletter:create_group.html.twig", array(
            'form' => $form->createView(),
            'parent' => $request->query->get('parent')
        ));
    }
    
    /***
     * Update group
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateGroupAction(Request $request, Group $group) {
        $form = $this->createForm(GroupUpdateType::class, $group, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_newsletter_group_update', ['id' => $group->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => $group->getName()], 'messages'));
            return $this->redirectToRoute('admin_newsletter_group_list');
        }
        
        return $this->render("AdminBundle:Newsletter:update_group.html.twig", array(
            'group' => $group,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete group
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
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
        return $this->redirectToRoute('admin_newsletter_group_list');
    }
    
    /***
     * List subscribers
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listSubscribersAction(Request $request) {
        return $this->render("AdminBundle:Newsletter:list_subscribers.html.twig", array(
            'subscribers' => $this->getDoctrine()->getRepository(Subscriber::class)->findBy([], ['createdAt' => 'DESC'])
        ));
    }
    
    /***
     * Create Subscriber
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createSubscriberAction(Request $request){
        $subscriber = new Subscriber();
        $form = $this->createForm(SubscriberCreateType::class, $subscriber, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_newsletter_subscriber_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($subscriber);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => $subscriber->getEmail()], 'messages'));
            return $this->redirectToRoute('admin_newsletter_subscriber_list');
        }
        
        return $this->render("AdminBundle:Newsletter:create_subscriber.html.twig", [
            'form' => $form->createView()
        ]);
    }
    
    /***
     * Update Subscriber
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateSubscriberAction(Request $request, Subscriber $subscriber){
        $form = $this->createForm(SubscriberUpdateType::class, $subscriber, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_newsletter_subscriber_update', ['id' => $subscriber->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => $subscriber->getEmail()], 'messages'));
            return $this->redirectToRoute('admin_newsletter_subscriber_list');
        }
        
        return $this->render("AdminBundle:Newsletter:update_subscriber.html.twig", [
            'subscriber' => $subscriber,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * Export subscribers
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function exportSubscriberAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $subscribers = null;
        $date = new \DateTime();
        $basename = $date->getTimestamp().'.csv';
        
        if ($groupId =  $request->query->get('group')) {
            $group = $em->getRepository(Group::class)->find($groupId);
            
            if ($group !== null) {
                $basename = $group->getName().'.csv';
                if ($group->getSubscribers() !== null) {
                    $subscribers = $group->getSubscribers();
                }
            }
        }else {
            $subscribers = $this->getDoctrine()->getRepository(Subscriber::class)->findAll();
        }
        
        $fs = new Filesystem();
        $dirname = File::getBaseDir().File::getBasePath().'/newsletter/subscribers';
        
        if (!$fs->exists($dirname)){
            $fs->mkdir($dirname);
        }
        
        if ($subscribers !== null ){
            $filename = $dirname.'/'.$basename;
            $fp = fopen($filename, 'w');
            fputcsv($fp, [
                $this->get('translator')->trans('newsletter.property.subscriber.full_name', [], 'messages'),
                $this->get('translator')->trans('newsletter.property.subscriber.email', [], 'messages'),
            ]);
            foreach ($subscribers as $subscriber){
                fputcsv($fp, [
                    $subscriber->getFullName(),
                    $subscriber->getEmail()
                ]);
            }
            
            fclose($fp);
        }
        
        $route = File::getBasePath().MediaUtil::extractContext(Subscriber::class).$basename;
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse([
                'status' => true,
                'href' => $route
            ]);
        }
        
        return $this->redirect($route);
    }
    
    /**
     * Import subscribers
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function importAction(Request $request) {
        if ($request->isMethod("POST") === true) {
            $folder = $this->get('media.file_manager')->createFolder(MediaUtil::extractContext(Subscriber::class), $this->getUser(), true);
            $media = $this->get('media.upload_manager')->prepareUpload($_FILES, $folder, $this->getUser());
            
            $filename = $media[0]->getAbsolutePath();
            $fp = fopen($filename, 'r');
            
            $count = 0;
            
            $em = $this->getDoctrine()->getManager();
            $group = null;
            
            while (feof($fp) === false) {
                $row = fgetcsv($fp);
                
                if (is_array($row) && (int)$row[0] !== false) {
                    $row[2] = trim($row[2]);
                    $subscriber = $em->getRepository(Subscriber::class)->findOneBy(['email' => $row[2]]);
                    
                    if ($row[5] === "" || $subscriber === null) {
                        $subscriber = new Subscriber();
                        $em->persist($subscriber);
                    }
                    
                    $subscriber->setName(trim($row[1]));
                    $subscriber->setEmail(trim($row[2]));
                    
                    $em->flush();
                    
                    $group = $em->getRepository(Group::class)->findOneBy(array('name' => $row[3]));
                    if ($group === null) {
                        $group = new Group();
                        $group->setName($row[3]);
                        
                        $em->persist($group);
                    }
                    
                    $group->addSubscriber($subscriber->getId());
                    
                    $em->flush();
                    $count++;
                }
            }
            
            $message = $this->get('translator')->trans('success.post', ['%item%' => $subscriber->getEmail()], 'messages');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            return $this->redirectToRoute('admin_newsletter_subscriber_list');
        }
        
        return $this->render("AdminBundle:Subscriber:import_subscriber.html.twig");
    }
    
    /**
     * Delete a subscriber
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteSubscriberAction(Request $request, Subscriber $subscriber) {
        $message = $this->get('translator')->trans('success.post', ['%item%' => $subscriber->getEmail()], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($subscriber);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_newsletter_subscriber_list');
    }
    
    /***
     * List templates
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listTemplatesAction(Request $request) {
        return $this->render("AdminBundle:Newsletter:list_templates.html.twig", array(
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
            'action' => $this->generateUrl('admin_newsletter_template_create')
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
            return $this->redirectToRoute('admin_newsletter_template_update', ['id' => $template->getId()]);
        }
        
        return $this->render("AdminBundle:Newsletter:create_template.html.twig", array(
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
            'action' => $this->generateUrl('admin_newsletter_template_update', ['id' => $template->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => $template->getName()], 'messages'));
            return $this->redirectToRoute('admin_newsletter_template_update', ['id' => $template->getId()]);
        }
        
        return $this->render("AdminBundle:Newsletter:update_template.html.twig", array(
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
        return $this->redirectToRoute('admin_newsletter_template_list');
    }
}
