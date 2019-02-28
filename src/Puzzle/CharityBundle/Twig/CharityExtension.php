<?php
namespace Puzzle\CharityBundle\Twig;

use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Paginator;
use Puzzle\CharityBundle\Entity\Cause;
use Puzzle\CharityBundle\Entity\Category;
use Puzzle\CharityBundle\Entity\Member;
use Puzzle\CharityBundle\Entity\Donation;
use Puzzle\CharityBundle\Entity\DonationLine;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class CharityExtension extends \Twig_Extension
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
            new \Twig_SimpleFunction('puzzle_charity_categories', [$this, 'getCategories'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_charity_category', [$this, 'getCategory'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_charity_causes', [$this, 'getCauses'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_charity_cause', [$this, 'getCause'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_charity_members', [$this, 'getMembers'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_charity_member', [$this, 'getMember'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_charity_donations', [$this, 'getDonations'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_charity_donation', [$this, 'getDonation'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_charity_donation_lines', [$this, 'getDonationLines'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_charity_donation_lint', [$this, 'getDonationLine'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    public function getCategories(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Category::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Category::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getCategory($id) {
        if (!$category = $this->em->find(Category::class, $id)) {
            $category =  $this->em->getRepository(Category::class)->findOneBy(['slug' => $id]);
        }
        
        return $category;
    }
    
    public function getMembers(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Member::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Member::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getMember($id) {
        return $this->em->find(Member::class, $id);
    }
    
    public function getCauses(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Cause::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Cause::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getCause($id) {
        if (!$cause = $this->em->find(Cause::class, $id)) {
            $cause =  $this->em->getRepository(Cause::class)->findOneBy(['slug' => $id]);
        }
        
        return $cause;
    }
    
    public function getDonations(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Donation::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Donation::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getDonation($id) {
        return $this->em->find(Donation::class, $id);
    }
    
    public function getDonationLines(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], int $limit = 5, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(DonationLine::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(DonationLine::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getDonationLine($id) {
        return $this->em->find(DonationLine::class, $id);
    }
}
