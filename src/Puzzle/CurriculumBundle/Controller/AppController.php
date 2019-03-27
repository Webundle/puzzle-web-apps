<?php
namespace Puzzle\CurriculumBundle\Controller;

use Puzzle\CurriculumBundle\Entity\Work;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Puzzle\CurriculumBundle\Entity\Applicant;
use Puzzle\CurriculumBundle\Entity\Training;

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
     * Show applicant
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showApplicantAction(Request $request, $id){
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $applicant = $em->find(Applicant::class, $id);
        
        return $this->render("AppBundle:Curriculum:list_applicants.html.twig", array(
            'applicant' => $applicant
        ));
    }
    
    /***
     * Show trainings
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listTrainingsAction(Request $request, $id){
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        
        $applicant = $em->find(Applicant::class, $id);
        $trainings = $em->getRepository(Training::class)->findBy(['applicant' => $id], ['startedAt' => 'ASC']);
        
        return $this->render("AppBundle:Curriculum:list_trainings.html.twig", array(
            'applicant' => $applicant,
            'trainings' => $trainings,
        ));
    }
    
    /***
     * Show training
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showTrainingAction(Request $request, $id){
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $training = $em->find(Training::class, $id);
        
        return $this->render("AppBundle:Curriculum:show_training.html.twig", array(
            'training' => $training
        ));
    }
    
    /***
     * Show works
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listWorksAction(Request $request, $id){
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        
        $applicant = $em->find(Applicant::class, $id);
        $works = $em->getRepository(Work::class)->findBy(['applicant' => $id], ['startedAt' => 'ASC']);
        
        return $this->render("AppBundle:Curriculum:list_works.html.twig", array(
            'applicant' => $applicant,
            'works' => $works,
        ));
    }
    
    /***
     * Show work
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showWorkAction(Request $request, $id){
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $work = $em->find(Work::class, $id);
        
        return $this->render("AppBundle:Curriculum:show_work.html.twig", array(
            'work' => $work
        ));
    }
}