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
            new \Twig_SimpleFunction('blog_category_by_slug', [$this, 'getCategoryBySlug'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('blog_posts_by_category', [$this, 'getPostsByCategory'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('blog_posts', [$this, 'getPosts'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('blog_post_flashes', [$this, 'getPostFlashes'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('blog_post_siblings', [$this, 'renderPostSiblings'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('blog_category_siblings', [$this, 'renderCategorySiblings'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('blog_post_tags', [$this, 'renderPostTags'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('blog_archives', [$this, 'renderArchives'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    public function getCategoryBySlug(string $slug) {
        return $this->em->getRepository(Category::class)->findOneBy(['slug' => $slug]);
    }
    
    public function getPosts($limit = 5, $page = 1) {
        $query = $this->em
                        ->createQueryBuilder()
                        ->select('p')
                        ->from(Post::class, 'p')
                        ->orderBy('p.createdAt', 'DESC')
                        ->getQuery();
        
        return $this->paginator->paginate($query, $page, $limit);
    }
    
    public function getPostsByCategory($category, $limit = 5, $page = 1, bool $deep = false) {
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
    
    public function getPostFlashes() {
        $query = $this->em
                    ->createQueryBuilder()
                    ->select('p')
                    ->from(Post::class, 'p')
                    ->where('p.isFlash = :isFlash')
                    ->andWhere('p.flashExpiresAt >= :now')
                    ->setParameters(['isFlash' => true, 'now' => date_format(new \DateTime(), 'Y-m-d')])
                    ->orderBy('p.createdAt', 'DESC')
                    ->getQuery();
        
        return $this->paginator->paginate($query);
    }
    
    /**
     * Render post siblings
     * 
     * @param Post $post
     * @param int $limit
     * @return Collection|NULL
     */
    public function renderPostSiblings(Post $post, int $limit = 5) {
        return $this->em->getRepository(Post::class)->customFindBy(
            null, null, [['category', $post->getCategory()->getId()], ['id', $post->getId(), '!=']], ['createdAt' => 'DESC'], $limit
        );
    }
    
    /**
     * Render category siblings
     * @param Category $category
     * @return @return Collection|NULL
     */
    public function renderCategorySiblings(Category $category, int $limit = 5) {
        $criteria = [['id', $category->getId(), '!=']];
        
        if ($category->getParentNode() !== null){
            $criteria[] = ['parentNode', $category->getParentNode()->getId()];
        }else {
            $criteria[] = ['parentNode', null, 'IS NULL'];
        }
        
        return $this->em->getRepository(Category::class)->customFindBy(
            null, null, $criteria, ['name' => 'ASC'], $limit
        );
    }
    
    /**
     * Render post cloud tags
     * @return array
     */
    public function renderPostTags() {
        $results = $this->em->getRepository(Post::class)->customFindBy(
            ['tags'], null,[['tags', null, 'IS NOT NULL']]
        );
        
        $tags = [];
        foreach ($results as $item) {
            $tags = $item['tags'] !== null ? array_unique(array_merge($tags, $item['tags'])) : $tags;
        }
        
        return $tags;
    }
    
    /**
     * Render archives
     * @return array
     */
    public function renderArchives() {
        return $this->em->getRepository(Archive::class)->findBy([], ['year' => 'ASC', 'month' => 'ASC']);
    }
}
