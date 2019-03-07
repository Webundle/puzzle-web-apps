<?php
namespace Puzzle\StaticBundle\Twig;

use Doctrine\ORM\EntityManager;
use Puzzle\StaticBundle\Entity\Template;
use Puzzle\StaticBundle\Entity\Page;

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
            new \Twig_SimpleFunction('puzzle_static_pages', [$this, 'getPages'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_static_page', [$this, 'getPage'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_static_templates', [$this, 'getTemplates'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_static_template', [$this, 'getTemplate'], ['needs_environment' => false, 'is_safe' => ['html']])
        ];
    }
    
    public function getPages(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Page::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Page::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getPage($id) {
        if (!$page = $this->em->find(Page::class, $id)) {
            $page =  $this->em->getRepository(Page::class)->findOneBy(['slug' => $id]);
        }
        
        return $page;
    }
    
    public function getTemplates(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Template::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Template::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getTemplate($id) {
        return $this->em->find(Template::class, $id);
    }
}
