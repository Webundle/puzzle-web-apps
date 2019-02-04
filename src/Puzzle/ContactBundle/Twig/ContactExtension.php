<?php
namespace Puzzle\ContactBundle\Twig;

use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Paginator;
use Puzzle\ContactBundle\Entity\Group;
use Puzzle\ContactBundle\Entity\Contact;
use Puzzle\ContactBundle\Entity\Request;


/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class ContactExtension extends \Twig_Extension
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
            new \Twig_SimpleFunction('puzzle_contact_groups', [$this, 'getGroups'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_contact_group', [$this, 'getGroups'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_contacts', [$this, 'getContacts'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_contact', [$this, 'getContact'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_contact_requests', [$this, 'getRequests'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_contact_request', [$this, 'getRequest'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    public function getGroups(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Group::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Group::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getGroup($id) {
        if (!$group = $this->em->find(Group::class, $id)) {
            $group =  $this->em->getRepository(Group::class)->findOneBy(['slug' => $id]);
        }
        
        return $group;
    }
    
    public function getContacts(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Contact::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Contact::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getContact($id) {
        return $this->em->find(Contact::class, $id);
    }
    
    public function getRequests(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Request::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Request::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getRequest($id) {
        return $this->em->find(Request::class, $id);
    }
}
