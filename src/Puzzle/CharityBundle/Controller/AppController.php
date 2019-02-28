<?php

namespace Puzzle\CharityBundle\Controller;

use Puzzle\CharityBundle\Entity\Category;
use Puzzle\CharityBundle\Entity\Cause;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\CharityBundle\Entity\Donation;
use Doctrine\ORM\EntityManager;
use Puzzle\CharityBundle\Entity\Member;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class AppController extends Controller
{
    /**
     * List members
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listMembersAction(Request $request) {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        
        return $this->render("AppBundle:Charity:list_members.html.twig", array(
            'members' => $em->getRepository(Member::class)->findBy([], ['createdAt' => 'DESC'])
        ));
    }
    
    /**
     * Show member
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showMemberAction(Request $request, $id) {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        return $this->render("AppBundle:Charity:show_member.html.twig", array(
            'cause' => $em->find(Member::class, $id)
        ));
    }
    
    /**
     * List causes
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCausesAction(Request $request) {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        
        return $this->render("AppBundle:Charity:list_causes.html.twig", array(
            'causes' => $em->getRepository(Cause::class)->findBy([], ['createdAt' => 'DESC'])
        ));
    }
    
    /**
     * Show Cause
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCauseAction(Request $request, $slug) {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
    	return $this->render("AppBundle:Charity:show_cause.html.twig", array(
    	    'cause' => $em->getRepository(Cause::class)->findOneBy(['slug' => $slug])
    	));
    }
    
    
    /***
     * List categories
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCategoriesAction(Request $request) {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        return $this->render("AppBundle:Charity:list_categories.html.twig", [
            'categories' => $em->getRepository(Category::class)->findBy(['parent' => null], ['createdAt' => 'DESC'])
        ]);
    }
    
    
    /***
     * Show Category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCategoryAction(Request $request, $id) {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $category = $em->find(Category::class, $id);
        
    	return $this->render("AppBundle:Charity:show_category.html.twig", array(
    	    'category' => $category,
    	    'causes' => $category->getCauses(),
    	));
    }
    
    /**
     * Create causeulate
     */
    public function createDonationAction(Request $request, $id) {
        $em = $this->get('doctrine.orm.entity_manager');
        $cause = $em->find(Cause::class, $id);
        $user = $this->getUser();
        $data = $request->request->all();
        
        $donation = new Donation();
        $donation->setUser($user);
        $donation->setCause($cause);
        $donation->setFirstName($data['firstName'] ?? $user->getFirstName());
        $donation->setLastName($data['lastName'] ?? $user->getLastName());
        $donation->setEmail($data['email'] ?? $user->getEmail());
        $donation->setPhoneNumber($data['phoneNumber'] ?? $user->getPhoneNumber());
        $donation->setTotalAmount($data['totalAmount']);
        $donation->setPaidAmount($data['paidAmount']);
        $donation->setAddress($data['address']);
        
        $em->persist($donation);
        $em->flush();
        
        return new JsonResponse(null, 204);
    }
}
