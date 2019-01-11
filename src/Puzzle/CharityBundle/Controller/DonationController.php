<?php
namespace Puzzle\CharityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Puzzle\CharityBundle\Entity\Donation;
use Puzzle\CharityBundle\Form\Type\DonationCreateType;
use Puzzle\CharityBundle\Entity\DonationLine;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Mpdf\Mpdf;

/**
 *
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 */
class DonationController extends Controller
{
	/**
	 * Show donations
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @Security("has_role('ROLE_CHARITY') or has_role('ROLE_ADMIN')")
	 */
    public function listAction(Request $request) {
    	 return $this->render('CharityBundle:Donation:list.html.twig', array(
    	     'donations' => $this->getDoctrine()->getRepository(Donation::class)->findAll()
    	 )); 
    }
    
    /**
     * Show Donation 
     * 
     * @param Request $request
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, $id) {
    	$donation = $this->getDoctrine()->getRepository(Donation::class)->find($id);
    	$content = $this->renderView("CharityBundle:Donation:show.html.twig", array('donation' => $donation));
    	
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
    	return $this->render('CharityBundle:Donation:new_charity_donation_form.html.twig',[
    		"number" => $request->get("count")
    	]);
    }
    

    /**
     * Create Donation
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $donation = new Donation();
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(DonationCreateType::class, $donation, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_charity_donation_create')
        ]);
    	
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
    	    $em = $this->getDoctrine()->getManager();
    	    $em->persist($donation);
    	    
    	    $paidAmount = 0;
    	    $data = $request->request->all();
    	    
    	    for ($i = 1; $i <= $data['count_form']; $i++) {
    	        $donationLine = new DonationLine();
    	        $donationLine->setDonatedAt(new \DateTime($data['donation_line_date_'.$i]));
    	        $donationLine->setAmount($data['donation_line_amount_'.$i]);
    	        $donationLine->setStatus($data['donation_line_status_'.$i]);
    	        $donationLine->setDonation($donation);
    	        
    	        $em->persist($donationLine);
    	        
    	        if ($data['donation_line_status_'.$i] == true) {
    	            $paidAmount += $data['donation_line_amount_'.$i];
    	        }
    	    }
    	    
    	    $donation->setPaidAmount($paidAmount);
    	    $donation->getCause()->setPaidAmount( $donation->getCause()->getPaidAmount() + $paidAmount);
    	    
    	    $em->flush();
    	    
    	    $this->addFlash('success', $this->get('translator')->trans('success.post', [], 'messages'));
    	    return $this->redirectToRoute('admin_charity_donation_update', ['id' => $donation->getId()]);
    	}
    	
    	return $this->render('CharityBundle:Donation:create.html.twig', array(
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
    public function updateAction(Request $request, Donation $donation)
    {
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(DonationCreateType::class, $donation, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_charity_donation_update', ['id'=> $donation->getId() ])
        ]);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($donation);
            
            $paidAmount = 0;
            $data = $request->request->all();
            $cause = $donation->getCause();
            
            if (isset($data['count_form'])) {
                $olders = [];
                
                foreach ($donation->getDonationLines() as $donationLine){
                    $olders[$donationLine->getId()] = $donationLine->getAmount();
                    $amount = $donationLine->getAmount();
                    if($donationLine->getStatus()){
                        $donation->setPaidAmount($donation->getPaidAmount() - $amount);
                        $cause->setPaidAmount($cause->getPaidAmount() - $amount);
                    }
                }
                
                $paidAmount = 0;
                for ($i = 1; $i <= $data['count_form']; $i++){
                    if(! isset($data['donation_line_'.$i])|| !$donationLine = $em->getRepository(DonationLine::class)->find($data['donation_line_'.$i])){
                        $donationLine = new DonationLine();
                        $donationLine->setDonation($donation);
                        
                        $em->persist($donationLine);
                    }
                    
                    $donationLine->setDonatedAt(new \DateTime($data['donation_line_date_'.$i]));
                    $donationLine->setAmount($data['donation_line_amount_'.$i]);
                    $donationLine->setStatus($data['donation_line_status_'.$i]);
                    
                    if($data['donation_line_status_'.$i] == true){
                        $paidAmount += $data['donation_line_amount_'.$i];
                    }
                }
                
                $donation->setPaidAmount($paidAmount);
                $cause->setPaidAmount($cause->getPaidAmount() + $paidAmount);
            }
            
            // Remove donationLines
            if(isset($data['lines_to_remove'])){
                $listDonationLinesId = explode(",", $data['lines_to_remove']);
                foreach ($listDonationLinesId as $donationLineId){
                    $donationLineToRemove = $em->getRepository("CharityBundle:DonationLine")->find($donationLineId);
                    if($donationLineToRemove){
                        $amount = $donationLineToRemove->getAmount();
                        if($donationLineToRemove->getStatus()){
                            $donation->setPaidAmount($donation->getPaidAmount() - $amount);
                            $cause->setPaidAmount($cause->getPaidAmount() - $amount);
                        }
                        
                        $em->remove($donationLineToRemove);
                    }
                }
            }
            
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
            return $this->redirectToRoute('admin_charity_donation_update', ['id' => $donation->getId()]);
        }
    	
    	return $this->redirectToRoute('charity_donation_update', array(
    	    'donation' => $donation,
    	    'form' => $form->createView()
    	));
    }
    
    
    /**
     * Delete Donation
     * 
     * @param Request $request
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Donation $donation)
    {
    	$em = $this->getDoctrine()->getManager();
    	$em->remove($donation);
    	$em->flush();
    	
    	$this->addFlash('success', $this->get('translator')->trans('success.delete', [], 'messages'));
    	return $this->redirectToRoute('admin_charity_donation_list');
    }
}
