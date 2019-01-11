<?php

namespace Puzzle\LearningBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Puzzle\LearningBundle\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 */
class CommentController extends Controller
{
    /***
     * Show Comments
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_LEARNING') or has_role('ROLE_ADMIN')")
     */
    public function listAction(Request $request)
    {
    	$comments = $this->getDoctrine()
    					->getRepository(Comment::class)
    					->findBy(['post' => $request->get('post')],['createdAt' => 'DESC']);
    	
    	return $this->render("LearningBundle:Comment:list.html.twig", array(
    			'comments' => $comments
    	));
    }
    
    
    /***
     * Update Comment Form
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_LEARNING') or has_role('ROLE_ADMIN')")
     */
    public function updateFormAction(Request $request, Comment $comment) {
    	return $this->render("LearningBundle:Comment:update.html.twig", array('comment' => $comment));
    }
    
    
    /**
     * Update Comment
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_LEARNING') or has_role('ROLE_ADMIN')")
     */
    public function updateCallbackAction(Request $request, $id)
    {
    	$data = $request->request->all();
    	$em = $this->getDoctrine()->getManager();
    	$comment = $em->getRepository(Comment::class)->find($id);
    	
    	if(isset($data['is_visible']) && $data['is_visible'] == "on"){
    		$comment->setIsVisbile(true);
    	}else{
    		$comment->setIsVisbile(false);
    	}
    	
    	$em->flush();
    
    	$response = new Response();
    	$response->headers->set('Content-Type', 'text/html');
    	$response->setContent(true);
    	
    	return $response;
    }
    
    
    /***
     * Delete comment
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_LEARNING') or has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$comment = $em->getRepository(Comment::class)->find($id);
    	
    	$em->remove($comment);
    	$em->flush();
    
    	$response = new Response();
    	$response->headers->set('Content-Type', 'text/html');
    	$response->setContent(true);
    	
    	return $response;
    }
}
