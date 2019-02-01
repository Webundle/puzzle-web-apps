<?php
namespace Puzzle\NewsletterBundle\Twig;

use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Paginator;
use Puzzle\NewsletterBundle\Entity\Group;
use Puzzle\NewsletterBundle\Entity\Subscriber;
use Puzzle\NewsletterBundle\Entity\Template;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class NewsletterExtension extends \Twig_Extension
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
            new \Twig_SimpleFunction('newsletter_groups', [$this, 'getGroups'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('newsletter_group', [$this, 'getGroups'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('newsletter_subscribers', [$this, 'getSubscribers'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('newsletter_subscriber', [$this, 'getSubscriber'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('newsletter_templates', [$this, 'getTemplates'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('newsletter_template', [$this, 'getTemplate'], ['needs_environment' => false, 'is_safe' => ['html']])
        ];
    }
    
    public function getGroups(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], int $limit = null, int $page = 1) {
        $query = $this->em->getRepository(Group::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
        return $this->paginator->paginate($query, $page, $limit);
    }
    
    public function getGroup($id) {
        if (!$group = $this->em->find(Group::class, $id)) {
            $group =  $this->em->getRepository(Group::class)->findOneBy(['slug' => $id]);
        }
        
        return $group;
    }
    
    public function getSubscribers(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], int $limit = null, int $page = 1) {
        $query = $this->em->getRepository(Subscriber::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
        return $this->paginator->paginate($query, $page, $limit);
    }
    
    public function getSubscriber($id) {
        if (!$subscriber = $this->em->find(Subscriber::class, $id)) {
            $subscriber =  $this->em->getRepository(Subscriber::class)->findOneBy(['slug' => $id]);
        }
        
        return $subscriber;
    }
    
    public function getTemplates(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], int $limit = null, int $page = 1) {
        $query = $this->em->getRepository(Template::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
        return $this->paginator->paginate($query, $page, $limit);
    }
    
    public function getTemplate($id) {
        return $this->em->find(Template::class, $id);
    }
}
