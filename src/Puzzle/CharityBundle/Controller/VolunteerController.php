<?php

namespace Puzzle\CharityBundle\Controller;

use Puzzle\CharityBundle\Entity\Staff;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * 
 * @author qwincy
 *
 */
class VolunteerController extends Controller
{
	/***
	 * Show Staffs
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function showListAction(Request $request)
	{
		$staffs = $this->getDoctrine()->getRepository("CharityBundle:Staff")->findAll();
		 
		return $this->render("CharityBundle:Staff:list.html.twig", array(
				'staffs' => $staffs
		));
	}

	/***
	 * Show Staff
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function showAction(Request $request, $id)
	{
		$staff = $this->getDoctrine()->getRepository("CharityBundle:Staff")->find($id);
		 
		return $this->render("CharityBundle:Staff:view.html.twig", array(
				'staff' => $staff
		));
	}

	/***
	 * Create Staff Form
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function createFormAction(Request $request)
	{
		return $this->render("CharityBundle:Staff:create.html.twig");
	}

	/**
	 * Create Staff Callback
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function createCallbackAction(Request $request)
	{
		$data = $request->request->all();
		 
		$staff = new Staff();
		$staff->setFirstName($data['first_name']);
		$staff->setLastName($data['last_name']);
		$staff->setPosition($data['position']);
		$staff->setAddress($data['address']);
		$staff->setDescription($data['description']);
		$staff->setPicture($data['picture']);
		$staff->setEmail($data['email']);
		$staff->setPhone($data['phone']);
		$staff->setSkype($data['skype']);
		$staff->setWhatsApp($data['whatsapp']);
		$staff->setFacebook($data['facebook']);
		$staff->setTwitter($data['twitter']);
		$staff->setLinkedIn($data['linkedin']);
		$staff->setGooglePlus($data['googleplus']);
		 
		$em = $this->getDoctrine()->getManager();
		$em->persist($staff);
		$em->flush();

		return $this->redirectToRoute('expertise_staffs');
	}

	/***
	 * Update Staff Form
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function updateFormAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$staff = $em->getRepository("CharityBundle:Staff")->find($id);
		
		return $this->render("CharityBundle:Staff:update.html.twig", array(
				'staff' => $staff
		));
	}

	/**
	 *
	 * Update Staff
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function updateCallbackAction(Request $request, $id)
	{
		$data = $request->request->all();
		$em = $this->getDoctrine()->getManager();

		$staff = $em->getRepository("CharityBundle:Staff")->find($id);
		$staff->setFirstName($data['first_name']);
		$staff->setLastName($data['last_name']);
		$staff->setPosition($data['position']);
		$staff->setAddress($data['address']);
		$staff->setDescription($data['description']);
		$staff->setEmail($data['email']);
		$staff->setPhone($data['phone']);
		$staff->setSkype($data['skype']);
		$staff->setWhatsApp($data['whatsapp']);
		$staff->setFacebook($data['facebook']);
		$staff->setTwitter($data['twitter']);
		$staff->setLinkedIn($data['linkedin']);
		$staff->setGooglePlus($data['googleplus']);
		$staff->setPicture($data['picture']);
		 
		$em->flush();

		return $this->redirectToRoute('expertise_staffs');
	}

	/***
	 * Remove Staff
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function removeAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$staff = $em->getRepository("CharityBundle:Staff")->find($id);
		 
		$em->remove($staff);
		$em->flush();

		return $this->redirectToRoute('expertise_staffs');
	}
}
