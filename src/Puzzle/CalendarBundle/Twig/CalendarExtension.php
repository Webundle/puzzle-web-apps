<?php
namespace Puzzle\CalendarBundle\Twig;

use Doctrine\ORM\EntityManager;
use Puzzle\CalendarBundle\Entity\Agenda;
use Puzzle\CalendarBundle\Entity\Moment;
use Knp\Component\Pager\Paginator;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class CalendarExtension extends \Twig_Extension
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
            new \Twig_SimpleFunction('calendar_agendas', [$this, 'getAgendas'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('calendar_agenda', [$this, 'getAgenda'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('calendar_moments', [$this, 'getMoments'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('calendar_moment', [$this, 'getMoment'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    public function getAgendas(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], int $limit = null, int $page = 1) {
        $query = $this->em->getRepository(Agenda::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
        return $this->paginator->paginate($query, $page, $limit);
    }
    
    public function getAgenda($id) {
        if (!$category = $this->em->find(Agenda::class, $id)) {
            $category =  $this->em->getRepository(Agenda::class)->findOneBy(['slug' => $id]);
        }
        
        return $category;
    }
    
    public function getMoments(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], int $limit = null, int $page = 1) {
        $query = $this->em->getRepository(Moment::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
        return $this->paginator->paginate($query, $page, $limit);
    }
    
    public function getMoment($id) {
        if (!$category = $this->em->find(Moment::class, $id)) {
            $category =  $this->em->getRepository(Moment::class)->findOneBy(['slug' => $id]);
        }
        
        return $category;
    }
}
