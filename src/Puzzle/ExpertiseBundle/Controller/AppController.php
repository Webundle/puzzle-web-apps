<?php
namespace Puzzle\ExpertiseBundle\Controller;

use Puzzle\ExpertiseBundle\Entity\Faq;
use Puzzle\ExpertiseBundle\Entity\Partner;
use Puzzle\ExpertiseBundle\Entity\Project;
use Puzzle\ExpertiseBundle\Entity\Service;
use Puzzle\ExpertiseBundle\Entity\Staff;
use Puzzle\ExpertiseBundle\Entity\Testimonial;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\ExpertiseBundle\Entity\Feature;
use Puzzle\ExpertiseBundle\Entity\Pricing;
use Puzzle\ExpertiseBundle\Entity\Contact;
use Puzzle\ExpertiseBundle\Event\ContactEvent;
use Puzzle\ExpertiseBundle\ExpertiseEvents;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class AppController extends Controller
{
    /***
     * Show Services
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listServicesAction(Request $request) {
        return $this->render("AppBundle:Expertise:list_services.html.twig", array(
            'services' => $this->getDoctrine()->getRepository(Service::class)->findAll()
        ));
    }

    /***
     * Show service
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showServiceAction(Request $request, $id) {
        return $this->render("AppBundle:Expertise:show_service.html.twig", array(
            'service' => $this->getDoctrine()->getRepository(Service::class)->find($id)
        ));
    }

    /***
     * Show Projects
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listProjectsAction(Request $request) {
        return $this->render("AppBundle:Expertise:list_projects.html.twig", array(
            'projects' => $this->getDoctrine()->getRepository(Project::class)->findAll()
        ));
    }

    /***
     * Show service
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showProjectAction(Request $request, $id) {
        return $this->render("AppBundle:Expertise:show_project.html.twig", array(
            'project' => $this->getDoctrine()->getRepository(Project::class)->find($id)
        ));
    }


    /***
     * Show Staffs
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listStaffsAction(Request $request) {
        return $this->render("AppBundle:Expertise:list_staffs.html.twig", array(
            'staffs' => $this->getDoctrine()->getRepository(Staff::class)->findAll()
        ));
    }

    /***
     * Show Partners
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPartnersAction(Request $request) {
        return $this->render("AppBundle:Expertise:list_partners.html.twig", array(
            'partners' => $this->getDoctrine()->getRepository(Partner::class)->findAll()
        ));
    }


    /***
     * Show Partner
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showPartnerAction(Request $request, $id) {
        return $this->render("AppBundle:Expertise:show_partner.html.twig", array(
            'partner' => $this->getDoctrine()->getRepository(Partner::class)->find($id)
        ));
    }

    /***
     * Show testimonials
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listTestimonialsAction(Request $request) {
        return $this->render("AppBundle:Expertise:list_testimonials.html.twig", array(
            'testimonials' => $this->getDoctrine()->getRepository(Testimonial::class)->findAll()
        ));
    }

    /***
     * Show testimonial
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showTestimonialAction(Request $request, $id) {
        return $this->render("AppBundle:Expertise:show_testimonial.html.twig", array(
            'testimonial' => $this->getDoctrine()->getRepository(Testimonial::class)->find($id)
        ));
    }

    /***
     * Show FAQs
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listFaqsAction(Request $request) {
        return $this->render("AppBundle:Expertise:list_faqs.html.twig", array(
            'faqs' => $this->getDoctrine()->getRepository(Faq::class)->findAll()
        ));
    }

    /***
     * Show FAQ
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFaqAction(Request $request, $id) {
        return $this->render("AppBundle:Expertise:show_faq.html.twig", array(
            'faq' => $this->getDoctrine()->getRepository(Faq::class)->find($id)
        ));
    }

    /***
     * List features
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listFeaturesAction(Request $request) {
        return $this->render("AppBundle:Expertise:list_features.html.twig", array(
            'features' => $this->getDoctrine()->getRepository(Feature::class)->findAll()
        ));
    }

    /***
     * Show feature
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFeatureAction(Request $request, $id) {
        return $this->render("AppBundle:Expertise:show_feature.html.twig", array(
            'feature' => $this->getDoctrine()->getRepository(Feature::class)->find($id)
        ));
    }

    /***
     * List pricings
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPricingsAction(Request $request) {
        return $this->render("AppBundle:Expertise:list_pricings.html.twig", array(
            'pricings' => $this->getDoctrine()->getRepository(Pricing::class)->findAll()
        ));
    }

    /***
     * Show pricing
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showPricingAction(Request $request, $id) {
        return $this->render("AppBundle:Expertise:show_pricing.html.twig", array(
            'pricing' => $this->getDoctrine()->getRepository(Pricing::class)->find($id)
        ));
    }

    /**
     * Create contact
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createContactAction(Request $request)
    {
        $data = $request->request->all();
        $redirectUri = $request->query->get('redirect_uri');
        $em = $this->getDoctrine()->getManager();

        $contact = new Contact();
        $em->persist($contact);

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

        if (isset($data['subject']) && $data['subject']) {
            $contact->setSubject($data['subject']);
        }

        if (isset($data['message']) && $data['message']) {
            $contact->setPhoneNumber($data['message']);
        }

        if (isset($data['service']) && $data['service']) {
            $service = $em->find(Service::class, $data['service']);
            $contact->setService($service);
        }

        $em->flush();

        $this->get('event_dispatcher')->dispatch(ExpertiseEvents::EXPERTISE_CONTACT_CREATED, new ContactEvent($contact));

        $message = $this->get('translator')->trans('app.expertise.contact.create.created', [], 'app');
        if ($request->isXmlHttpRequest() === true){
            return new JsonResponse($message);
        }

        $this->addFlash('success', $message);
        return $this->redirect($redirectUri);
    }
}
