<?php
namespace Puzzle\AdvertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;

/**
 * Advert Category
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 * @ORM\Table(name="advert_category")
 * @ORM\Entity(repositoryClass="Puzzle\AdvertBundle\Repository\CategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Category
{
    use PrimaryKeyTrait,
        Timestampable,
        Blameable,
        Sluggable
    ;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     * @var string
     */
    private $name;
    
    /**
     * @ORM\Column(name="description", type="string", length=255)
     * @var string
     */
    private $description;
    
    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="category")
     */
    private $posts;
    
    /**
     * Constructor
     */
    public function __construct() {
    	$this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getSluggableFields() {
        return ['name'];
    }
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription() {
        return $this->description;
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
