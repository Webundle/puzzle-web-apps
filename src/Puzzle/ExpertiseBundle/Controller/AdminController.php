<?php
namespace Puzzle\ExpertiseBundle\Controller;

use Puzzle\ExpertiseBundle\Entity\Service;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\ExpertiseBundle\Form\Type\ServiceCreateType;
use Puzzle\ExpertiseBundle\Form\Type\ServiceUpdateType;
use Puzzle\ExpertiseBundle\Entity\Project;
use Puzzle\ExpertiseBundle\Form\Type\ProjectCreateType;
use Puzzle\ExpertiseBundle\Form\Type\ProjectUpdateType;
use Puzzle\ExpertiseBundle\Entity\Staff;
use Puzzle\ExpertiseBundle\Form\Type\StaffCreateType;
use Puzzle\ExpertiseBundle\Form\Type\StaffUpdateType;
use Puzzle\ExpertiseBundle\Entity\Partner;
use Puzzle\ExpertiseBundle\Form\Type\PartnerCreateType;
use Puzzle\ExpertiseBundle\Form\Type\PartnerUpdateType;
use Puzzle\ExpertiseBundle\Entity\Testimonial;
use Puzzle\ExpertiseBundle\Form\Type\TestimonialCreateType;
use Puzzle\ExpertiseBundle\Form\Type\TestimonialUpdateType;
use Puzzle\ExpertiseBundle\Entity\Faq;
use Puzzle\ExpertiseBundle\Form\Type\FaqCreateType;
use Puzzle\ExpertiseBundle\Form\Type\FaqUpdateType;
use Puzzle\ExpertiseBundle\Entity\Feature;
use Symfony\Component\HttpFoundation\JsonResponse;
use Puzzle\ExpertiseBundle\Form\Type\FeatureCreateType;
use Puzzle\ExpertiseBundle\Form\Type\FeatureUpdateType;
use Puzzle\ExpertiseBundle\Entity\Pricing;
use Puzzle\ExpertiseBundle\Form\Type\PricingCreateType;
use Puzzle\ExpertiseBundle\Form\Type\PricingUpdateType;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class AdminController extends Controller
{
    /***
     * Show Services
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listServicesAction(Request $request) {
        return $this->render("AdminBundle:Expertise:list_services.html.twig", array(
            'services' => $this->getDoctrine()->getRepository(Service::class)->findAll()
        ));
    }
    
    /***
     * Create service
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createServiceAction(Request $request) {
        $service = new Service();
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(ServiceCreateType::class, $service, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_expertise_service_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_expertise_service_create'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Service::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($service) {
                        $service->setPicture($filename);
                    }
                 ]));
            }
            
            $em->persist($service);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', [], 'messages'));
            return $this->redirectToRoute('admin_expertise_service_update', ['id' => $service->getId()]);
        }
        
        $previousService = $em->getRepository(Service::class)->findOneBy([], ['createdAt' => 'DESC']);
        
        return $this->render("AdminBundle:Expertise:create_service.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Update service
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateServiceAction(Request $request, Service $service) {
        $form = $this->createForm(ServiceUpdateType::class, $service, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_expertise_service_update', ['id' => $service->getId()])
        ]);
        $form->handleRequest($request);
        
        $em = $this->getDoctrine()->getManager();
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_expertise_service_update'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($service->getPicture() === null || $service->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Service::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($service) {
                        $service->setPicture($filename);
                    }
                ]));
            }
            
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', [], 'messages'));
            return $this->redirectToRoute('admin_expertise_service_update', ['id' => $service->getId()]);
        }
        
        $lastService = $em->getRepository(Service::class)->findOneBy([], ['createdAt' => 'DESC']);
        
        return $this->render("AdminBundle:Expertise:update_service.html.twig", array(
            'service' => $service,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete service
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteServiceAction(Request $request, Service $service) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($service);
        $em->flush();
        
        $this->addFlash('success', $this->get('translator')->trans('success.delete', [], 'messages'));
        return $this->redirectToRoute('admin_expertise_service_list');
    }
    
    
    /***
     * Show Projects
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listProjectsAction(Request $request) {
        return $this->render("AdminBundle:Expertise:list_projects.html.twig", array(
            'projects' => $this->getDoctrine()->getRepository(Project::class)->findAll()
        ));
    }
    
    
    /**
     *
     * Create project
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createProjectAction(Request $request) {
        $project = new Project();
        $form = $this->createForm(ProjectCreateType::class, $project, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_expertise_project_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_expertise_project_create'];
            
            $project->setStartedAt($data['startedAt']);
            $project->setEndedAt($data['endedAt']);
            
            $pictures = $request->request->get('pictures') !== null ? $request->request->get('pictures') : $data['pictures'];
            
            if ($pictures !== null) {
                $project->setPictures([]);
                $pictures = explode(',', $pictures);
                
                foreach ($pictures as $picture) {
                    $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                        'path' => $picture,
                        'context' => MediaUtil::extractContext(Project::class),
                        'user' => $this->getUser(),
                        'closure' => function($filename) use ($project) {
                        $project->addPicture($filename);
                        }
                        ]));
                }
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', [], 'messages'));
            return $this->redirectToRoute('admin_expertise_project_update', ['id' => $project->getId()]);
        }
        
        return $this->render("AdminBundle:Expertise:create_project.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    /**
     *
     * Update project
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateProjectAction(Request $request, Project $project) {
        $form = $this->createForm(ProjectUpdateType::class, $project, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_expertise_project_update', ['id' => $project->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_expertise_project_update'];
            
            $project->setStartedAt($data['startedAt']);
            $project->setEndedAt($data['endedAt']);
            
            $pictures = $request->request->get('pictures') !== null ? $request->request->get('pictures') : $data['pictures'];
            
            if ($project->getPictures() === null || $pictures !== implode(',', $project->getPictures())) {
                $project->setPictures([]);
                $pictures = explode(',', $pictures);
                
                foreach ($pictures as $picture) {
                    $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                        'path' => $picture,
                        'context' => MediaUtil::extractContext(Project::class),
                        'user' => $this->getUser(),
                        'closure' => function($filename) use ($project) {
                        $project->addPicture($filename);
                        }
                        ]));
                }
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
            return $this->redirectToRoute('admin_expertise_project_update', ['id' => $project->getId()]);
        }
        
        return $this->render("AdminBundle:Expertise:update_projects.html.twig", array(
            'project'  => $project,
            'form'     => $form->createView()
        ));
    }
    
    /***
     * Delete Project
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProjectAction(Request $request, Project $project) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($project);
        $em->flush();
        
        $this->addFlash('success', $this->get('translator')->trans('success.delete', [], 'messages'));
        return $this->redirectToRoute('admin_expertise_project_list');
    }
    
    
    /***
     * Show Staffs
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listStaffsAction(Request $request) {
        return $this->render("AdminBundle:Expertise:list_staffs.html.twig", array(
            'staffs' => $this->getDoctrine()->getRepository(Staff::class)->findAll()
        ));
    }
    
    /***
     * Create staff
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createStaffAction(Request $request) {
        $staff = new Staff();
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(StaffCreateType::class, $staff, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_expertise_staff_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_expertise_staff_create'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Staff::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($staff) {$staff->setPicture($filename);}
                ]));
            }
            
            $em->persist($staff);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', [], 'messages'));
            return $this->redirectToRoute('admin_expertise_staff_update', ['id' => $staff->getId()]);
        }
        
        return $this->render("AdminBundle:Expertise:create_staff.html.twig", array(
            'form' => $form->createView(),
            'parent' => $request->query->get('parent')
        ));
    }
    
    
    /***
     * Update staff
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateStaffAction(Request $request, Staff $staff) {
        $form = $this->createForm(StaffUpdateType::class, $staff, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_expertise_staff_update', ['id' => $staff->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_expertise_staff_update'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($staff->getPicture() === null || $staff->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Staff::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($staff) {$staff->setPicture($filename);}
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
            return $this->redirectToRoute('admin_expertise_staff_update', ['id' => $staff->getId()]);
        }
        
        return $this->render("AdminBundle:Expertise:update_staff.html.twig", array(
            'staff' => $staff,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete staff
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteStaffAction(Request $request, Staff $staff) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($staff);
        $em->flush();
        
        $this->addFlash('success', $this->get('translator')->trans('success.delete', [], 'messages'));
        return $this->redirectToRoute('admin_expertise_staff_list');
    }
    
    /***
     * Show Partners
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPartnersAction(Request $request) {
        return $this->render("AdminBundle:Expertise:list_partners.html.twig", array(
            'partners' => $this->getDoctrine()->getRepository(Partner::class)->findAll()
        ));
    }
    
    /***
     * Create partner
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createPartnerAction(Request $request){
        $partner = new Partner();
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(PartnerCreateType::class, $partner, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_expertise_partner_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $partner->setTags($partner->getTags() !== null ? explode(',', $partner->getTags()) : null);
            
            $data = $request->request->all()['admin_expertise_partner_create'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Partner::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($partner) {$partner->setPicture($filename);}
                ]));
            }
            
            $em->persist($partner);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', [], 'messages'));
            return $this->redirectToRoute('admin_expertise_partner_list');
        }
        
        return $this->render("AdminBundle:Expertise:create_partner.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Update partner
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updatePartnerAction(Request $request, Partner $partner) {
        $form = $this->createForm(PartnerUpdateType::class, $partner, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_expertise_partner_update', ['id' => $partner->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $partner->setTags($partner->getTags() !== null ? explode(',', $partner->getTags()) : null);
            
            $data = $request->request->all()['admin_expertise_partner_update'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($partner->getPicture() === null || $partner->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Partner::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($partner) {$partner->setPicture($filename);}
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
            return $this->redirectToRoute('admin_expertise_partner_list');
        }
        
        return $this->render("AdminBundle:Expertise:update_partner.html.twig", array(
            'partner' => $partner,
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Delete partner
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletePartnerAction(Request $request, Partner $partner) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($partner);
        $em->flush();
        
        $this->addFlash('success', $this->get('translator')->trans('success.delete', [], 'messages'));
        return $this->redirectToRoute('admin_expertise_partner_list');
    }
    
    
    /***
     * Show testimonials
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listTestimonialsAction(Request $request) {
        return $this->render("AdminBundle:Expertise:list_testimonials.html.twig", array(
            'testimonials' => $this->getDoctrine()->getRepository(Testimonial::class)->findAll()
        ));
    }
    
    /***
     * Create testimonial
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createTestimonialAction(Request $request) {
        $testimonial = new Testimonial();
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(TestimonialCreateType::class, $testimonial, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_expertise_testimonial_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_expertise_testimonial_create'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Testimonial::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($testimonial) {$testimonial->setPicture($filename);}
                ]));
            }
            
            $em->persist($testimonial);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', [], 'messages'));
            return $this->redirectToRoute('admin_expertise_testimonial_update', ['id' => $testimonial->getId()]);
        }
        
        return $this->render("AdminBundle:Expertise:create_testimonial.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Update testimonial
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateTestimonialAction(Request $request, Testimonial $testimonial)
    {
        $form = $this->createForm(TestimonialUpdateType::class, $testimonial, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_expertise_testimonial_update', ['id' => $testimonial->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_expertise_testimonial_update'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($testimonial->getPicture() === null || $testimonial->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $testimonial->getPicture(),
                    'context' => MediaUtil::extractContext(Testimonial::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($testimonial) {
                    $testimonial->setPicture($filename);
                    }
                    ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
            return $this->redirectToRoute('admin_expertise_testimonial_update', ['id' => $testimonial->getId()]);
        }
        
        return $this->render("AdminBundle:Expertise:update_testimonial.html.twig", array(
            'testimonial' => $testimonial,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete testimonial
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteTestimonialAction(Request $request, Testimonial $testimonial)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($testimonial);
        $em->flush();
        
        $this->addFlash('success', $this->get('translator')->trans('success.delete', [], 'messages'));
        return $this->redirectToRoute('admin_expertise_testimonial_list');
    }
    
    
    /***
     * Show FAQs
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listFaqsAction(Request $request) {
        return $this->render("AdminBundle:Expertise:list_faqs.html.twig", array(
            'faqs' => $this->getDoctrine()->getRepository(Faq::class)->findAll()
        ));
    }
    
    /***
     * Create Faq
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createFaqAction(Request $request) {
        $faq = new Faq();
        $form = $this->createForm(FaqCreateType::class, $faq, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_expertise_faq_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($faq);
            $em->flush();
            
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', [], 'messages'));
            return $this->redirectToRoute('admin_expertise_faq_update', ['id' => $faq->getId()]);
        }
        
        return $this->render("AdminBundle:Expertise:create_faq.html.twig", [
            'form' => $form->createView(),
        ]);
    }
    
    /***
     * Update Faq Form
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateFaqAction(Request $request, Faq $faq) {
        $form = $this->createForm(FaqUpdateType::class, $faq, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_expertise_faq_update', ['id' => $faq->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
            return $this->redirectToRoute('admin_expertise_faq_update', ['id' => $faq->getId()]);
        }
        
        return $this->render("AdminBundle:Expertise:update_faq.html.twig", [
            'faq' => $faq,
            'form' => $form->createView(),
        ]);
    }
    
    /***
     * Delete Faq
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteFaqAction(Request $request, Faq $faq) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($faq);
        $em->flush();
        
        $this->addFlash('success', $this->get('translator')->trans('success.delete', [], 'messages'));
        return $this->redirectToRoute('admin_expertise_faq_list');
    }
    
    /***
     * List features
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listFeaturesAction(Request $request) {
        return $this->render("AdminBundle:Expertise:list_features.html.twig", array(
            'features' => $this->getDoctrine()->getRepository(Feature::class)->findAll()
        ));
    }
    
    /***
     * Create feature
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createFeatureAction(Request $request) {
        $feature = new Feature();
        $form = $this->createForm(FeatureCreateType::class, $feature, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_expertise_feature_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($feature);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', [], 'messages'));
            return $this->redirectToRoute('admin_expertise_feature_update', ['id' => $feature->getId()]);
        }
        
        return $this->render("AdminBundle:Expertise:create_feature.html.twig", [
            'form' => $form->createView(),
        ]);
    }
    
    /***
     * Update feature
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateFeatureAction(Request $request, Feature $feature) {
        $form = $this->createForm(FeatureUpdateType::class, $feature, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_expertise_feature_update', ['id' => $feature->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
            return $this->redirectToRoute('admin_expertise_feature_update', ['id' => $feature->getId()]);
        }
        
        return $this->render("AdminBundle:Expertise:update_feature.html.twig", [
            'feature' => $feature,
            'form' => $form->createView(),
        ]);
    }
    
    /***
     * Delete feature
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteFeatureAction(Request $request, Feature $feature) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($feature);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(null, 204);
        }
        
        
        $this->addFlash('success', $this->get('translator')->trans('success.delete', [], 'messages'));
        return $this->redirectToRoute('admin_expertise_feature_list');
    }
    
    /***
     * List pricings
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPricingsAction(Request $request) {
        return $this->render("AdminBundle:Expertise:list_pricings.html.twig", array(
            'pricings' => $this->getDoctrine()->getRepository(Pricing::class)->findAll()
        ));
    }
    
    /***
     * Create pricing
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createPricingAction(Request $request) {
        $pricing = new Pricing();
        $form = $this->createForm(PricingCreateType::class, $pricing, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_expertise_pricing_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pricing);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', [], 'messages'));
            return $this->redirectToRoute('admin_expertise_pricing_update', ['id' => $pricing->getId()]);
        }
        
        return $this->render("AdminBundle:Expertise:create_pricing.html.twig", [
            'form' => $form->createView(),
        ]);
    }
    
    /***
     * Update pricing
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updatePricingAction(Request $request, Pricing $pricing) {
        $form = $this->createForm(PricingUpdateType::class, $pricing, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_expertise_pricing_update', ['id' => $pricing->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
            return $this->redirectToRoute('admin_expertise_pricing_update', ['id' => $pricing->getId()]);
        }
        
        return $this->render("AdminBundle:Expertise:update_pricing.html.twig", [
            'pricing' => $pricing,
            'form' => $form->createView(),
        ]);
    }
    
    /***
     * Delete pricing
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletePricingAction(Request $request, Pricing $pricing) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($pricing);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(null, 204);
        }
        
        $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
        return $this->redirectToRoute('admin_expertise_pricing_list');
    }
}
