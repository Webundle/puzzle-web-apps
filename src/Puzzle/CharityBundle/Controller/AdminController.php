<?php
namespace Puzzle\CharityBundle\Controller;

use Puzzle\CharityBundle\Entity\Category;
use Puzzle\CharityBundle\Entity\Cause;
use Puzzle\CharityBundle\Form\Type\CategoryCreateType;
use Puzzle\CharityBundle\Form\Type\CategoryUpdateType;
use Puzzle\CharityBundle\Form\Type\CauseCreateType;
use Puzzle\CharityBundle\Form\Type\CauseUpdateType;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\CharityBundle\Entity\Donation;
use Puzzle\CharityBundle\Form\Type\DonationCreateType;
use Puzzle\CharityBundle\Form\Type\DonationUpdateType;
use Puzzle\CharityBundle\Entity\DonationLine;
use Mpdf\Mpdf;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;
use Puzzle\CharityBundle\Entity\Member;
use Puzzle\CharityBundle\Form\Type\MemberUpdateType;
use Puzzle\CharityBundle\Form\Type\MemberCreateType;
use Puzzle\CharityBundle\Event\MemberEvent;
use Puzzle\CharityBundle\CharityEvents;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class AdminController extends Controller
{
    /***
     * List members
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listMembersAction(Request $request) {
        return $this->render("AdminBundle:Charity:list_members.html.twig", array(
            'members' => $this->getDoctrine()->getRepository(Member::class)->findBy([], ['createdAt' => 'DESC'])
        ));
    }
    
    /***
     * Create Member
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createMemberAction(Request $request){
        $member = new Member();
        $form = $this->createForm(MemberCreateType::class, $member, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_charity_member_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_charity_member_create'];
            if ($user = $member->getUser()) {
                $member->setEmail($member->getEmail() ?? $user->getEmail());
                $member->setFirstName($member->getFirstName() ?? $user->getFirstName());
                $member->setLastName($member->getLastName() ?? $user->getLastName());
                $member->setPhoneNumber($member->getPhoneNumber() ?? $user->getPhoneNumber());
            }
            
//             $enabled = $member->isEnabled() == "on" ? true : false;
//             $member->setEnabled($enabled);
            $em = $this->getDoctrine()->getManager();
            $em->persist($member);
            $em->flush();
            
            if (! empty($data['createAccount']) && $data['createAccount'] == 1) {
                $this->get('event_dispatcher')->dispatch(CharityEvents::CHARITY_MEMBER_CREATED, new MemberEvent($member));
            }
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => $member->getEmail()], 'messages'));
            return $this->redirectToRoute('admin_charity_member_list');
        }
        
        return $this->render("AdminBundle:Charity:create_member.html.twig", [
            'form' => $form->createView()
        ]);
    }
    
    /***
     * Update Member
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateMemberAction(Request $request, Member $member){
        $form = $this->createForm(MemberUpdateType::class, $member, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_charity_member_update', ['id' => $member->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
//             $enabled = $member->isEnabled() == "on" ? true : false;
//             $member->setEnabled($enabled);
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => $member->getEmail()], 'messages'));
            return $this->redirectToRoute('admin_charity_member_list');
        }
        
        return $this->render("AdminBundle:Charity:update_member.html.twig", [
            'member' => $member,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * Delete a member
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteMemberAction(Request $request, Member $member) {
        $message = $this->get('translator')->trans('success.delete', ['%item%' => $member->getEmail()], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($member);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_charity_member_list');
    }
    
    /***
     * Show categories
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCategoriesAction(Request $request) {
        return $this->render("AdminBundle:Charity:list_categories.html.twig", array(
            'categories' => $this->getDoctrine()->getRepository(Category::class)->findAll()
        ));
    }
    
    
    /***
     * Show category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCategoryAction(Request $request, Category $category) {
        return $this->render("AdminBundle:Charity:show_category.html.twig", array(
            'category' => $category
        ));
    }
    
    
    /***
     * Create category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createCategoryAction(Request $request) {
        $category = new Category();
        $em = $this->getDoctrine()->getManager();
        $parentId = $request->query->get('parent');
        
        if ($parentId === true && $parent = $em->getRepository(Category::class)->find($parentId)){
            $category->setParentNode($parent);
        }
        
        $form = $this->createForm(CategoryCreateType::class, $category, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_charity_category_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em->persist($category);
            $em->flush();
            
            $message = $this->get('translator')->trans('success.post', [
                '%item%' => $category->getName()
            ], 'messages');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            return $this->redirectToRoute('admin_charity_category_list');
        }
        
        return $this->render("AdminBundle:Charity:create_category.html.twig", array(
            'form' => $form->createView(),
            'parent' => $request->query->get('parent')
        ));
    }
    
    
    /***
     * Update category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateCategoryAction(Request $request, Category $category) {
        $form = $this->createForm(CategoryUpdateType::class, $category, [
            'method' => 'POST', 
            'action' => $this->generateUrl('admin_charity_category_update', ['id' => $category->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $message = $this->get('translator')->trans('success.put', [
                '%item%' => $category->getName()
            ], 'messages');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            return $this->redirectToRoute('admin_charity_category_list');
        }
        
        return $this->render("AdminBundle:Charity:update_category.html.twig", array(
            'category' => $category,
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Delete category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCategoryAction(Request $request, Category $category) {
        $message = $this->get('translator')->trans('success.delete', [
            '%item%' => $category->getName()
        ], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_charity_category_list');
    }
    
    /***
     * Show Causes
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCausesAction(Request $request){
        return $this->render("AdminBundle:Charity:list_causes.html.twig", array(
            'causes' => $this->getDoctrine()->getRepository(Cause::class)->findAll()
        ));
    }
    
    /***
     * Create cause
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createCauseAction(Request $request) {
        $cause = new Cause();
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(CauseCreateType::class, $cause, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_charity_cause_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_charity_cause_create'];
            
            $visible = $cause->isVisible() == "on" ? true : false;
            $cause->setVisible($visible);
            
            if (empty($data['expiresAt']) === false) {
                $cause->setExpiresAt(new \DateTime($data['expiresAt']));
            }
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Cause::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($cause) {
                        $cause->setPicture($filename);
                    }
                ]));
            }
            
            $em->persist($cause);
            $em->flush();
            
            $message = $this->get('translator')->trans('success.post', [
                '%item%' => $cause->getName()
            ], 'messages');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            return $this->redirectToRoute('admin_charity_cause_update', ['id' => $cause->getId()]);
        }
        
        return $this->render("AdminBundle:Charity:create_cause.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Update cause
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateCauseAction(Request $request, Cause $cause) {
        $form = $this->createForm(CauseUpdateType::class, $cause, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_charity_cause_update', ['id' => $cause->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_charity_cause_update'];
            
            $visible = $cause->isVisible() == "on" ? true : false;
            $cause->setVisible($visible);
            
            if (empty($data['expiresAt']) === false) {
                $cause->setExpiresAt(new \DateTime($data['expiresAt']));
            }
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            if ($cause->getPicture() === null || $cause->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Cause::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($cause) {
                    $cause->setPicture($filename);
                    }
                 ]));
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $message = $this->get('translator')->trans('success.put', [
                '%item%' => $cause->getName()
            ], 'messages');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            return $this->redirectToRoute('admin_charity_cause_update', ['id' => $cause->getId()]);
        }
        
        return $this->render("AdminBundle:Charity:update_cause.html.twig", array(
            'cause' => $cause,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete Cause
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCauseAction(Request $request, Cause $cause) {
        $message = $this->get('translator')->trans('success.delete', [
            '%item%' => $cause->getName()
        ], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($cause);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_charity_cause_list');
    }
    
    /**
	 * List donations
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function listDonationsAction(Request $request) {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $criteria = empty($request->query->get('cause')) === false ? ['cause' => $request->query->get('cause')] : [];
        
    	 return $this->render('AdminBundle:Charity:list_donations.html.twig', array(
    	     'donations' =>  $em->getRepository(Donation::class)->findBy($criteria, ['createdAt' => 'ASC'])
    	 )); 
    }
    
    /**
     * Show Donation 
     * 
     * @param Request $request
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDonationAction(Request $request, $id) {
    	$donation = $this->getDoctrine()->getRepository(Donation::class)->find($id);
    	$content = $this->renderView("AdminBundle:Charity:show_donation.html.twig", array('donation' => $donation));
    	
    	$mpdf = new Mpdf();
    	$mpdf->WriteHTML($content);
    	$mpdf->Output();
    }
    
    /**
     * 
     * Helper for adding new line
     * 
     * @param Request $request
     * @return Response
     */
    public function addDonationLineFormAction(Request $request) {
    	return $this->render('AdminBundle:Charity:add_donation_line.html.twig',[
    		"number" => $request->get("count")
    	]);
    }
    

    /**
     * Create Donation
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createDonationAction(Request $request)
    {
        $donation = new Donation();
        
        $form = $this->createForm(DonationCreateType::class, $donation, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_charity_donation_create')
        ]);
    	
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $cause = $donation->getCause();
            $paidAmount = $donation->getPaidAmount();
            
            $donation->setPaidAmount($paidAmount);
            $cause->setPaidAmount($cause->getPaidAmount() + $paidAmount);
    	    
    	    /** @var EntityManager $em */
    	    $em = $this->get('doctrine.orm.entity_manager');
    	    $em->persist($donation);
    	    
    	    $em->flush();
    	    
    	    $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => $donation->getMember()], 'messages'));
    	    return $this->redirectToRoute('admin_charity_donation_list');
    	}
    	
    	return $this->render('AdminBundle:Charity:create_donation.html.twig', array(
    		'form' => $form->createView()
    	));
    }
    
    /**
     * Update Donation
     * 
     * @param Request $request
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateDonationAction(Request $request, Donation $donation)
    {
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(DonationCreateType::class, $donation, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_charity_donation_update', ['id'=> $donation->getId() ])
        ]);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $cause = $donation->getCause();
            $paidAmount = $donation->getPaidAmount();
            
            $donation->setPaidAmount($paidAmount);
            $cause->setPaidAmount($cause->getPaidAmount() + $paidAmount);
            
            /** @var EntityManager $em */
            $em = $this->get('doctrine.orm.entity_manager');
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => $donation->getMember()], 'messages'));
            return $this->redirectToRoute('admin_charity_donation_list');
        }
    	
        return $this->render('AdminBundle:Charity:update_donation.html.twig', array(
            'donation' => $donation,
            'form' => $form->createView()
        ));
    }
    
    /**
     * Update Donation
     *
     * @param Request $request
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateDonationLinesAction(Request $request, Donation $donation)
    {
        $em = $this->getDoctrine()->getManager();
        
        if ($request->isMethod('POST') === true) {
            $data = $request->request->all();
            $paidAmount = $donation->getPaidAmount();
            $cause = $donation->getCause();
            
            for ($i = 0; $i <= $donation->getCountDonationLines() - 1; $i++) {
                if (! empty($data['donation_line_date_'.$i]) && ! empty($data['donation_line_amount_'.$i])) {
                    if (! empty($data['donation_line_'.$i])) {
                        $donationLine = $em->find(DonationLine::class, $data['donation_line_'.$i]);
                    }else {
                        $donationLine = new DonationLine();
                        $donationLine->setDonation($donation);
                        
                        $em->persist($donationLine);
                    }
                    
                    $donationLine->setDonatedAt(new \DateTime($data['donation_line_date_'.$i]));
                    $donationLine->setAmount($data['donation_line_amount_'.$i]);
                    $donationLine->setStatus($data['donation_line_status_'.$i]);
                    
                    if ($data['donation_line_status_'.$i] == true) {
                        $paidAmount += $data['donation_line_amount_'.$i];
                    }
                }
            }
            
            $donation->setPaidAmount($paidAmount);
            $cause->setPaidAmount($cause->getPaidAmount() + $paidAmount);
            
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => $donation->getMember()], 'messages'));
            return $this->redirectToRoute('admin_charity_donation_update_lines', ['id' => $donation->getId()]);
        }
        
        return $this->render('AdminBundle:Charity:update_donation_lines.html.twig', array(
            'donation' => $donation,
            'donationLines' => $donation->getDonationLines()
        ));
    }
    
    
    /**
     * Delete Donation
     * 
     * @param Request $request
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteDonationAction(Request $request, Donation $donation)
    {
        $message = $this->get('translator')->trans('success.delete', [
            '%item%' => $donation->getMember()
        ], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($donation);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
    	return $this->redirectToRoute('admin_charity_donation_list');
    }
}
