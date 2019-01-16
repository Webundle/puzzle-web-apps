<?php
namespace Puzzle\BlogBundle\Twig;

use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Paginator;
use Puzzle\BlogBundle\Entity\Post;
use Puzzle\BlogBundle\Entity\Category;
use Doctrine\Common\Collections\Collection;
use Puzzle\BlogBundle\Entity\Archive;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class BlogExtension extends \Twig_Extension
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
            new \Twig_SimpleFunction('blog_categories', [$this, 'getCategories'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('blog_posts_by_category', [$this, 'getPostsByCategory'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('blog_posts', [$this, 'getPosts'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('blog_archives', [$this, 'getArchives'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    /**
     * Get archives
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return Collection | NULL
     */
    public function getArchives(array $criteria = [], array $orderBy = ['year' => 'ASC', 'month' => 'ASC'], int $limit = null, int $offset = null) {
        return $this->em->getRepository(Archive::class)->findBy($criteria, $orderBy, $limit, $offset);
    }
    
    /**
     * Get categories
     *
     * @param array $fields
     * @param array $joins
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $page
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getCategories(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], int $limit = null, int $page = 1) {
        $query = $this->em->getRepository(Category::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null, null);
        return $this->paginator->paginate($query, $page, $limit);
    }
    
    /**
     * Get posts
     * 
     * @param array $fields
     * @param array $joins
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $page
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getPosts(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], int $limit = null, int $page = 1) {
        $query = $this->em->getRepository(Post::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null, null);
        return $this->paginator->paginate($query, $page, $limit);
    }
    
    /**
     * Get posts by category
     * 
     * @param string $category
     * @param int $limit
     * @param int $page
     * @param bool $deep
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getPostsByCategory(string $category, int $limit = 5, int $page = 1, bool $deep = false) {
        $queryBuilder = $this->em
                      ->createQueryBuilder()
                      ->select('p')
                      ->from(Post::class, 'p')
                      ->join('p.category', 'c');
        
        if ($deep === true) {
            $queryBuilder->join('c.parentNode', 'cp');
        }
        
        $queryBuilder->where('c.id = :category')->orWhere('c.slug = :category');
        
        if ($deep === true) {
            $queryBuilder->orWhere('cp.slug = :category');
        }
                      
        $query = $queryBuilder->setParameters(['category' => $category])
                              ->orderBy('p.createdAt', 'DESC')
                              ->getQuery();
        
        return $this->paginator->paginate($query, $page, $limit);
    }
}
