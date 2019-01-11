<?php

namespace Puzzle\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * 
 * @author qwincy
 *
 */
class ContactController extends Controller
{
    /***
     * Show Requests
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showListAction(Request $request)
    {
    	$contacts = $this->getDoctrine()->getRepository("MailBundle:Contact")->findBy(
    			array(), array('isRead' => 'DESC', 'createdAt' => 'DESC')
    	);
    	
    	return $this->render("MailBundle:Contact:list.html.twig", array(
    		'contacts' => $contacts,
    	));
    }
    
    /**
     *
     * Update Request
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $id)
    {
    	$data = $request->request->all();
    	$em = $this->getDoctrine()->getManager();
    	 
    	$contact = $em->getRepository("MailBundle:Contact")->find($id);
    	$contact->setIsRead(true);
    	
    	$em->persist($request);
    	$em->flush();
    
    	return $this->redirectToRoute('admin_mailcontacts');
    }
    
    /***
     * Remove Request
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$contact = $em->getRepository("MailBundle:Contact")->find($id);
    	
    	$em->remove($contact);
    	$em->flush();
    
    	return $this->redirectToRoute('admin_mailcontacts');;
    }
}
