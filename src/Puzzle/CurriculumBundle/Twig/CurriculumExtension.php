<?php
namespace Puzzle\CurriculumBundle\Twig;

use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Paginator;
use Puzzle\CurriculumBundle\Entity\Work;
use Puzzle\CurriculumBundle\Entity\Training;
use Puzzle\CurriculumBundle\Entity\Applicant;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class CurriculumExtension extends \Twig_Extension
{
    /**
     * @var EntityManager $em
     */
    protected $em;
    
    /**
     * @var Paginator $paginator
     */
    protected $paginator;
    
    public function __construct(EntityManager $em, Paginator $paginator) {
        $this->em = $em;
        $this->paginator = $paginator;
    }
    
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('puzzle_curriculum_works', [$this, 'getWorks'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_curriculum_work', [$this, 'getWork'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_curriculum_trainings', [$this, 'getTrainings'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_curriculum_training', [$this, 'getTraining'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_curriculum_applicants', [$this, 'getApplicants'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_curriculum_applicant', [$this, 'getApplicant'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    public function getWorks(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Work::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Work::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getWork($id) {
        return $this->em->find(Work::class, $id);
    }
    
    public function getTrainings(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Training::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Training::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getTraining($id) {
        return $this->em->find(Training::class, $id);
    }
    
    public function getApplicants(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Applicant::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Applicant::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getApplicant($id) {
        return $this->em->find(Applicant::class, $id);
    }
}
