<?php
namespace Puzzle\ExpertiseBundle\Twig;

use Doctrine\ORM\EntityManager;
use Puzzle\ExpertiseBundle\Entity\Faq;
use Puzzle\ExpertiseBundle\Entity\Feature;
use Puzzle\ExpertiseBundle\Entity\Partner;
use Puzzle\ExpertiseBundle\Entity\Pricing;
use Puzzle\ExpertiseBundle\Entity\Project;
use Puzzle\ExpertiseBundle\Entity\Service;
use Puzzle\ExpertiseBundle\Entity\Staff;
use Puzzle\ExpertiseBundle\Entity\Testimonial;
use Knp\Component\Pager\Paginator;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class ExpertiseExtension extends \Twig_Extension
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
            new \Twig_SimpleFunction('puzzle_expertise_faqs', [$this, 'getFaqs'], ['needs_environment' => false, 'is_safe' => ['html']]),            new \Twig_SimpleFunction('expertise_faqs', [$this, 'getFaqs'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_expertise_faq', [$this, 'getFaq'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_expertise_features', [$this, 'getFeatures'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_expertise_feature', [$this, 'getFeature'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_expertise_partners', [$this, 'getPartners'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_expertise_partner', [$this, 'getPartner'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_expertise_pricings', [$this, 'getPricings'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_expertise_pricing', [$this, 'getPricing'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_expertise_projects', [$this, 'getProjects'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_expertise_project', [$this, 'getProject'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_expertise_services', [$this, 'getServices'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_expertise_service', [$this, 'getService'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_expertise_staffs', [$this, 'getStaffs'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_expertise_staff', [$this, 'getStaff'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_expertise_testimonials', [$this, 'getTestimonials'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_expertise_testimonial', [$this, 'getTestimonial'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    public function getFaqs(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Faq::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Faq::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getFaq($id) {
        return $this->em->find(Faq::class, $id);
    }
    
    public function getFeatures(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Feature::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Feature::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getFeature($id) {
        return $this->em->find(Feature::class, $id);
    }
    
    public function getPartners(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Partner::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Partner::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getPartner($id) {
        return $this->em->find(Partner::class, $id);
    }
    
    public function getPricings(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Pricing::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Pricing::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getPricing($id) {
        return $this->em->find(Pricing::class, $id);
    }
    
    public function getProjects(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Project::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Project::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getProject($id) {
        if (!$project = $this->em->find(Project::class, $id)) {
            $project =  $this->em->getRepository(Project::class)->findOneBy(['slug' => $id]);
        }
        
        return $project;
    }
    
    public function getServices(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Service::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Service::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getService($id) {
        if (!$service = $this->em->find(Service::class, $id)) {
            $service =  $this->em->getRepository(Service::class)->findOneBy(['slug' => $id]);
        }
        
        return $service;
    }
    
    public function getStaffs(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Staff::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Staff::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getStaff($id) {
        return $this->em->find(Staff::class, $id);
    }
    
    public function getTestimonials(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Testimonial::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Testimonial::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getTestimonial($id) {
        return $this->em->find(Testimonial::class, $id);
    }
}
