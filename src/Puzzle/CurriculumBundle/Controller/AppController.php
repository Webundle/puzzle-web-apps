<?php
namespace Puzzle\CurriculumBundle\Controller;

use Puzzle\CurriculumBundle\Entity\Work;
use Puzzle\CurriculumBundle\Form\Type\WorkCreateType;
use Puzzle\CurriculumBundle\Form\Type\WorkUpdateType;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Mpdf\Mpdf;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;
use Puzzle\CurriculumBundle\Entity\Applicant;
use Puzzle\CurriculumBundle\Form\Type\ApplicantUpdateType;
use Puzzle\CurriculumBundle\Form\Type\ApplicantCreateType;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Puzzle\CurriculumBundle\Entity\Training;
use Puzzle\CurriculumBundle\Form\Type\TrainingCreateType;
use Puzzle\CurriculumBundle\Form\Type\TrainingUpdateType;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class AppController extends Controller
{
    /***
     * List applicants
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listApplicantsAction(Request $request) {
        return $this->render("AppBundle:Curriculum:list_applicants.html.twig", array(
            'applicants' => $this->getDoctrine()->getRepository(Applicant::class)->findBy([], ['createdAt' => 'DESC'])
        ));
    }
    
    /***
     * Create Applicant
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createApplicantAction(Request $request){
        $applicant = new Applicant();
        $form = $this->createForm(ApplicantCreateType::class, $applicant, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_curriculum_applicant_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['app_curriculum_applicant_create'];
            
            if ($user = $applicant->getUser()) {
                $applicant->setEmail($applicant->getEmail() ?? $user->getEmail());
                $applicant->setFirstName($applicant->getFirstName() ?? $user->getFirstName());
                $applicant->setLastName($applicant->getLastName() ?? $user->getLastName());
                $applicant->setPhoneNumber($applicant->getPhoneNumber() ?? $user->getPhoneNumber());
            }
            
            $skills = $applicant->getSkills() !== null ? explode(',', $applicant->getSkills()) : null;
            $applicant->setSkills($skills);
            
            $hobbies = $applicant->getHobbies() !== null ? explode(',', $applicant->getHobbies()) : null;
            $applicant->setHobbies($hobbies);
            
            $applicant->setBirthday(new \DateTime($data['birthday']));
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Applicant::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($applicant) {$applicant->setPicture($filename);}
                ]));
            }
            
            $file = $request->request->get('file') !== null ? $request->request->get('file') : $data['file'];
            if ($file !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $file,
                    'context' => MediaUtil::extractContext(Applicant::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($applicant) {$applicant->setFile($filename);}
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($applicant);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => $applicant->getEmail()], 'messages'));
            return $this->redirectToRoute('app_curriculum_applicant_update', ['id' => $applicant->getId()]);
        }
        
        return $this->render("AppBundle:Curriculum:create_applicant.html.twig", [
            'form' => $form->createView()
        ]);
    }
    
    /***
     * Update Applicant
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateApplicantAction(Request $request, Applicant $applicant){
        $form = $this->createForm(ApplicantUpdateType::class, $applicant, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_curriculum_applicant_update', ['id' => $applicant->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['app_curriculum_applicant_update'];
            
            $skills = $applicant->getSkills() !== null ? explode(',', $applicant->getSkills()) : null;
            $applicant->setSkills($skills);
            
            $hobbies = $applicant->getHobbies() !== null ? explode(',', $applicant->getHobbies()) : null;
            $applicant->setHobbies($hobbies);
            
            $applicant->setBirthday(new \DateTime($data['birthday']));
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            if ($applicant->getPicture() === null || $applicant->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Applicant::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($applicant) {$applicant->setPicture($filename);}
                ]));
            }
            
            $file = $request->request->get('file') !== null ? $request->request->get('file') : $data['file'];
            if ($applicant->getFile() === null || $applicant->getFile() !== $file) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $file,
                    'context' => MediaUtil::extractContext(Applicant::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($applicant) {$applicant->setFile($filename);}
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => $applicant->getEmail()], 'messages'));
            return $this->redirectToRoute('app_curriculum_applicant_update', ['id' => $applicant->getId()]);
        }
        
        return $this->render("AppBundle:Curriculum:update_applicant.html.twig", [
            'applicant' => $applicant,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * Delete a applicant
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteApplicantAction(Request $request, Applicant $applicant) {
        $message = $this->get('translator')->trans('success.delete', ['%item%' => $applicant->getEmail()], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($applicant);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('app_curriculum_applicant_list');
    }
    
    /***
     * Show trainings
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listTrainingsAction(Request $request, Applicant $applicant) {
        $trainings = $this->getDoctrine()->getRepository(Training::class)->findBy(['applicant' => $applicant->getId()], ['startedAt' => 'ASC']);
        
        return $this->render("AppBundle:Curriculum:list_trainings.html.twig", array(
            'applicant' => $applicant,
            'trainings' => $trainings,
        ));
    }
    
    /***
     * Create training
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createTrainingAction(Request $request, Applicant $applicant){
        $training = new Training();
        $training->setApplicant($applicant);
        
        $form = $this->createForm(TrainingCreateType::class, $training, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_curriculum_training_create', ['id' => $applicant->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['app_curriculum_training_create'];
            
            $training->setStartedAt(new \DateTime($data['startedAt']));
            $training->setEndedAt(new \DateTime($data['endedAt']));
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($training);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => $training->getName()], 'messages'));
            return $this->redirectToRoute('app_curriculum_training_list', ['id' => $applicant->getId()]);
        }
        
        return $this->render("AppBundle:Curriculum:create_training.html.twig", [
            'form' => $form->createView(),
        ]);
    }
    
    /***
     * Update training
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateTrainingAction(Request $request, Training $training){
        $form = $this->createForm(TrainingUpdateType::class, $training, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_curriculum_training_update', ['id' => $training->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['app_curriculum_training_create'];
            
            $training->setStartedAt(new \DateTime($data['startedAt']));
            $training->setEndedAt(new \DateTime($data['endedAt']));
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', ['%item%' => $training->getName()], 'messages'));
            return $this->redirectToRoute('app_curriculum_training_update', ['id' => $training->getId()]);
        }
        
        return $this->render("AppBundle:Curriculum:update_training.html.twig", [
            'training' => $training,
            'form' => $form->createView(),
        ]);
    }
    
    
    /**
     * Delete a training
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteTrainingAction(Request $request, Training $training) {
        $message = $this->get('translator')->trans('success.delete', ['%item%' => $training->getName()], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($training);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('app_curriculum_training_list', ['id' => $training->getApplicant()->getId()]);
    }
    
    /***
     * Show works
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listWorksAction(Request $request, Applicant $applicant) {
        $works = $this->getDoctrine()->getRepository(Work::class)->findBy(['applicant' => $applicant->getId()], ['startedAt' => 'ASC']);
        
        return $this->render("AppBundle:Curriculum:list_works.html.twig", array(
            'applicant' => $applicant,
            'works' => $works,
        ));
    }
    
    /***
     * Create work
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createWorkAction(Request $request, Applicant $applicant){
        $work = new Work();
        $work->setApplicant($applicant);
        
        $form = $this->createForm(WorkCreateType::class, $work, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_curriculum_work_create', ['id' => $applicant->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['app_curriculum_work_create'];
            
            $work->setStartedAt(new \DateTime($data['startedAt']));
            $work->setEndedAt(new \DateTime($data['endedAt']));
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($work);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => $work->getName()], 'messages'));
            return $this->redirectToRoute('app_curriculum_work_list', ['id' => $applicant->getId()]);
        }
        
        return $this->render("AppBundle:Curriculum:create_work.html.twig", [
            'form' => $form->createView(),
        ]);
    }
    
    /***
     * Update work
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateWorkAction(Request $request, Work $work){
        $form = $this->createForm(WorkUpdateType::class, $work, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_curriculum_work_update', ['id' => $work->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['app_curriculum_work_update'];
            
            $work->setStartedAt(new \DateTime($data['startedAt']));
            $work->setEndedAt(new \DateTime($data['endedAt']));
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('success.post', ['%item%' => $work->getName()], 'messages'));
            return $this->redirectToRoute('app_curriculum_work_list', ['id' => $work->getApplicant()->getId()]);
        }
        
        return $this->render("AppBundle:Curriculum:create_work.html.twig", [
            'work' => $work,
            'form' => $form->createView(),
        ]);
    }
    
    
    /**
     * Delete a work
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteWorkAction(Request $request, Work $work) {
        $message = $this->get('translator')->trans('success.delete', ['%item%' => $work->getName()], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($work);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('app_curriculum_work_list', ['id' => $work->getApplicant()->getId()]);
    }
}
