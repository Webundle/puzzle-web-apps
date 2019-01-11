<?php
namespace Puzzle\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Tree\Node;
use Knp\DoctrineBehaviors\Model\Tree\NodeInterface;
use Puzzle\AdminBundle\Traits\Describable;
use Puzzle\AdminBundle\Traits\Nameable;
use Puzzle\MediaBundle\Traits\Pictureable;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;

/**
 * Blog Category
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 * @ORM\Table(name="blog_category")
 * @ORM\Entity(repositoryClass="Puzzle\BlogBundle\Repository\CategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Category implements NodeInterface
{
    use PrimaryKeyTrait,
        Nameable,
        Describable,
        Pictureable,
        Timestampable,
        Blameable,
        Sluggable,
        Node
    ;

    /**
     * @var string
     * @ORM\Column(name="materializedPath", type="string", nullable=true)
     */
    protected $materializedPath = '';
    
    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="category")
     */
    private $posts;
    
    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parentNode")
     */
    private $childNodes;
    
    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="childNodes")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parentNode;
    
    /**
     * Constructor
     */
    public function __construct() {
    	$this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getSluggableFields() {
        return ['name'];
    }
    
    public function setPosts(Collection $posts) : self {
        $this->posts = $posts;
        return $this;
    }
    
    public function addPost(Post $post) : self {
        if ($this->posts === null || $this->posts->contains($post) === false){
            $this->posts->add($post);
        }
        
        return $this;
    }

    public function removePost(Post $post) : self {
        $this->posts->removeElement($post);
        return $this;
    }

    public function getPosts() :? Collection {
        return $this->posts;
    }
}
