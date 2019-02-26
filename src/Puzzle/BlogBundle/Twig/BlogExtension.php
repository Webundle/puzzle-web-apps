<?php
namespace Puzzle\BlogBundle\Twig;

use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Paginator;
use Puzzle\BlogBundle\Entity\Post;
use Puzzle\BlogBundle\Entity\Category;
use Puzzle\BlogBundle\Entity\Archive;
use Puzzle\BlogBundle\Entity\Comment;
use Puzzle\BlogBundle\Entity\CommentVote;

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
            new \Twig_SimpleFunction('puzzle_blog_categories', [$this, 'getCategories'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_blog_category', [$this, 'getCategory'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_blog_posts', [$this, 'getPosts'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_blog_post', [$this, 'getPost'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_blog_archives', [$this, 'getArchives'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_blog_archive', [$this, 'getArchive'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_blog_comments', [$this, 'getComments'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_blog_comment', [$this, 'getComment'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_blog_comment_votes', [$this, 'getCommentVotes'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_blog_comment', [$this, 'getCommentVote'], ['needs_environment' => false, 'is_safe' => ['html']]),
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
    
    public function getArchives(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Archive::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Archive::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getArchive($id) {
        return $this->em->find(Archive::class, $id);
    }
    
    public function getPosts(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Post::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Post::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getPost($id) {
        if (!$post = $this->em->find(Post::class, $id)) {
            $post =  $this->em->getRepository(Post::class)->findOneBy(['slug' => $id]);
        }
        
        return $post;
    }
    
    public function getComments(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Comment::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Comment::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getComment($id) {
        return $this->em->find(Comment::class, $id);
    }
    
    public function getCommentVotes(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], int $limit = 5, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(CommentVote::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(CommentVote::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getCommentVote($id) {
        return $this->em->find(CommentVote::class, $id);
    }
}
