<?php
namespace Puzzle\StaticBundle\Twig;

use Doctrine\ORM\EntityManager;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class StaticExtension extends \Twig_Extension
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
            new \Twig_SimpleFunction('static_page_by_slug', [$this, 'getPageBySlug'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('static_pages_by_parent', [$this, 'getPagesByParent'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    public function getPageBySlug($slug) {
        return $this->em->getRepository("StaticBundle:Page")->findOneBy(['slug' => $slug]);
    }
    
    public function getPagesByParent($parentId) {
        return $this->em->getRepository("StaticBundle:Page")->findBy(['parent' => $parentId]);
    }
}
