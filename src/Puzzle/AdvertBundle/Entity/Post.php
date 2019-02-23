<?php
namespace Puzzle\AdvertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;

/**
 * Post
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 * @ORM\Table(name="advert_post")
 * @ORM\Entity(repositoryClass="Puzzle\AdvertBundle\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Post
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
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    private $description;
    
    /**
     * @ORM\Column(name="tag", type="string", length=255, nullable=true)
     * @var string
     */
    private $tag;
    
    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(name="pinned", type="boolean")
     * @var boolean
     */
    private $pinned;
    
    /**
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $expiresAt;
    
    /**
     * @ORM\Column(name="enable_postulate", type="boolean")
     * @var boolean
     */
    private $enablePostulate;
    
    /**
     * @ORM\OneToMany(targetEntity="Postulate", mappedBy="post")
     */
    private $postulates;
    
    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="posts")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;
    
    /**
     * @ORM\ManyToOne(targetEntity="Advertiser", inversedBy="posts")
     * @ORM\JoinColumn(name="advertiser_id", referencedColumnName="id")
     */
    private $advertiser;
    
    /**
     * @ORM\ManyToOne(targetEntity="Archive", inversedBy="posts")
     * @ORM\JoinColumn(name="archive_id", referencedColumnName="id")
     */
    private $archive;
    
    /**
     * Constructor
     */
    public function __construct() {
    	$this->pinned = false;
    	$this->enablePostulate = true;
    	$this->postulates = new \Doctrine\Common\Collections\ArrayCollection();
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
    
    public function setTag(string $tag) :self {
        $this->tag = $tag;
        return $this;
    }
    
    public function getTag() :?string {
        return $this->tag;
    }
    
    public function setEmail(string $email) :self {
        $this->email = $email;
        return $this;
    }
    
    public function getEmail() :?string {
        return $this->email;
    }
    
    public function setPinned(bool $pinned) : self {
        $this->pinned = $pinned;
        return $this;
    }
    
    public function isPinned() :? bool {
        return $this->pinned;
    }
    
    public function setCategory(Category $category) : self {
        $this->category = $category;
        return $this;
    }

    public function getCategory() :? Category {
        return $this->category;
    }
    
    public function setAdvertiser(Advertiser $advertiser) :self {
        $this->advertiser = $advertiser;
        return $this;
    }
    
    public function getAdvertiser() :?Advertiser {
       return $this->advertiser;
    }
    
    public function setArchive(Archive $archive) : self {
        $this->archive = $archive;
        return $this;
    }
    
    public function getArchive() :? Archive {
        return $this->archive;
    }
    
    public function setExpiresAt(\DateTime $expiresAt) :self {
        $this->expiresAt = $expiresAt;
        return $this;
    }
    
    public function getExpiresAt() :?\DateTime {
        return $this->expiresAt;
    }
    
    public function setEnablePostulate(bool $enablePostulate) :self {
        $this->enablePostulate = $enablePostulate;
        return $this;
    }
    
    public function getEnablePostulate() :?bool {
        return $this->enablePostulate;
    }
    
    public function setPostulates(Collection $postulates) : self {
        $this->postulates = $postulates;
        return $this;
    }
    
    public function addPostulate(Postulate $postulate) : self {
        if ($this->postulates === null || $this->postulates->contains($postulate) === false){
            $this->postulates->add($postulate);
        }
        
        return $this;
    }
    
    public function removePostulate(Postulate $postulate) : self {
        $this->postulates->removeElement($postulate);
        return $this;
    }
    
    public function getPostulates() :? Collection {
        return $this->postulates;
    }
    
    public function __toString(){
        return $this->getName();
    }
}
