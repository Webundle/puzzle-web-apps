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
    
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('expertise_faqs', [$this, 'getFeatures'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('expertise_features', [$this, 'getFeatures'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('expertise_partners', [$this, 'getPartners'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('expertise_pricings', [$this, 'getPartners'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('expertise_projects', [$this, 'getProjects'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('expertise_services', [$this, 'getServices'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('expertise_staffs', [$this, 'getStaffs'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('expertise_testimonials', [$this, 'getTestimonials'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    public function getFaqs(array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $offset = null) {
        return $this->em->getRepository(Faq::class)->findBy($criteria, $orderBy, $limit, $offset);
    }
    
    public function getFeatures(array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $offset = null) {
        return $this->em->getRepository(Feature::class)->findBy($criteria, $orderBy, $limit, $offset);
    }
    
    public function getPartners(array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $offset = null) {
        return $this->em->getRepository(Partner::class)->findBy($criteria, $orderBy, $limit, $offset);
    }
    
    public function getPricings(array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $offset = null) {
        return $this->em->getRepository(Pricing::class)->findBy($criteria, $orderBy, $limit, $offset);
    }
    
    public function getProjects(array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $offset = null) {
        return $this->em->getRepository(Project::class)->findBy($criteria, $orderBy, $limit, $offset);
    }
    
    public function getServices(array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $offset = null) {
        return $this->em->getRepository(Service::class)->findBy($criteria, $orderBy, $limit, $offset);
    }
    
    public function getStaffs(array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $offset = null) {
        return $this->em->getRepository(Staff::class)->findBy($criteria, $orderBy, $limit, $offset);
    }
    
    public function getTestimonials(array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $offset = null) {
        return $this->em->getRepository(Testimonial::class)->findBy($criteria, $orderBy, $limit, $offset);
    }
}
