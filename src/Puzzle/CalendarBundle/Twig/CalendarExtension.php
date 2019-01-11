<?php
namespace Puzzle\CalendarBundle\Twig;

use Doctrine\ORM\EntityManager;

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
    
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('calendar_moments', [$this, 'getMoments'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    public function getMoments($limit, $order) {
        return $this->em->getRepository("CalendarBundle:Moment")->customFindBy(
            null, null, [], ['startedAt' => 'ASC'], $limit, null, true
        );
    }
}
