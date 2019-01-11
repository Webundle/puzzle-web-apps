<?php
namespace Puzzle\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Puzzle\MailBundle\Entity\Category;

class CategoryController extends Controller
{
	/***
	 * Show Categories
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function showListAction(Request $request)
	{
		$categories = $this->getDoctrine()->getRepository("MailBundle:Category")->findAll();
		
		return $this->render("MailBundle:Category:list.html.twig", array(
				'categories' => $categories,
				'subactive' => 'admin_mail_mails'
		));
	}
	
	/***
	 * Show Category
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function showAction(Request $request, $id)
	{
		$category = $this->getDoctrine()->getRepository("MailBundle:Category")->find($id);
		$templates = $this->getDoctrine()->getRepository('MailBundle:Template')->findAll();
		$categories = $this->getDoctrine()->getRepository('MailBundle:Category')->findAll();
		$subscribers = $this->getDoctrine()->getRepository('MailBundle:Subscriber')->findAll();
		$groups = $this->getDoctrine()->getRepository('MailBundle:GroupSubscriber')->findAll();
		
		$mails = $this->getDoctrine()
						->getRepository("MailBundle:Mail")
						->findBy(array('category' => $category->getId()), array('updatedAt' => 'DESC'));
		
		return $this->render("MailBundle:Category:view.html.twig", array(
				'category' => $category,
				'categories' => $categories,
				'mails' => $mails,
				'templates' => $templates,
				'subscribers' => $subscribers,
				'groups' => $groups,
				'subactive' => 'admin_mail_mails'
		));
	}
	
	/***
	 * Create Category Form
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function createFormAction(Request $request)
	{
		return $this->render("MailBundle:Category:create.html.twig");
	}
	
	/**
	 * Create a category
	 *
	 * @param unknown $idCategory
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function createCallbackAction(Request $request)
	{
		$data = $request->request->all();
		$em = $this->getDoctrine()->getManager();
		
		$category = new Category();
		$category->setName($data['name']);
		$category->setTag(str_replace(' ', '-', strtolower($data['name'])));
		$category->setDescription($data['description']);
		$category->setIsSystem(false);
		
		$em->persist($category);
		$em->flush();
		
		return $this->redirect($this->generateUrl('admin_mail_mails'));
	}
	
	
	/***
	 * Update Category Form
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function updateFormAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$category = $em->getRepository("MailBundle:Category")->find($id);
		
		$content = $this->renderView("MailBundle:Category:update.html.twig", array(
				'category' => $category,
		));
		
		$response = new Response();
		$response->headers->set('Content-Type', 'text/html');
		$response->setContent($content);
		
		return $response;
	}
	
	/**
	 * Edit a category
	 *
	 * @param unknown $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function updateCallbackAction($id, Request $request)
	{
		$data = $request->request->all();
		$em = $this->getDoctrine()->getManager();
		
		$category = $em->getRepository("MailBundle:Category")->find($id);
		$category->setName($data['name']);
		$category->setDescription($data['description']);
		
		$em->persist($category);
		$em->flush();
		
		return $this->redirect($this->generateUrl('admin_mail_mails'));
	}
	
	
	
	/**
	 * Remove a category
	 *
	 * @param unknown $idCategory
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function removeAction($id, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$category = $em->getRepository('MailBundle:Category')->findOneBy(array('id' => $id));
		
		$em->remove($category);
		$em->flush();
		
		return $this->redirect($this->generateUrl('admin_mail_mails'));
	}
}
