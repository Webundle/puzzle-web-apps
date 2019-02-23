<?php
namespace Puzzle\AdvertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;

/**
 * Advert Advertiser
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 * @ORM\Table(name="advert_advertiser")
 * @ORM\Entity(repositoryClass="Puzzle\AdvertBundle\Repository\AdvertiserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Advertiser
{
    use PrimaryKeyTrait,
        Timestampable,
        Blameable
    ;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     * @var string
     */
    private $name;
    
    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @var string
     */
    private $description;
    
    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @var string
     */
    private $email;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $picture;
    
    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="advertiser")
     */
    private $posts;
    
    /**
     * Constructor
     */
    public function __construct() {
    	$this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function setName($name) :self {
        $this->name = $name;
        return $this;
    }
    
    public function getName() :?string {
        return $this->name;
    }
    
    public function setDescription($description) :self {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription() :?string {
        return $this->description;
    }
    
    public function setEmail(string $email) :self {
        $this->email = $email;
        return $this;
    }
    
    public function getEmail() :?string {
        return $this->email;
    }
    
    public function setPicture($picture) :self {
        $this->picture = $picture;
        return $this;
    }
    
    public function getPicture() :?string {
        return $this->picture;
    }
    
    public function setPosts(Collection $posts) :self {
        $this->posts = $posts;
        return $this;
    }
    
    public function addPost(Post $post) :self {
        if ($this->posts === null || $this->posts->contains($post) === false){
            $this->posts->add($post);
        }
        
        return $this;
    }

    public function removePost(Post $post) :self {
        $this->posts->removeElement($post);
        return $this;
    }

    public function getPosts() :?Collection {
        return $this->posts;
    }
}
