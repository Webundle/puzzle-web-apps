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
     * @ORM\Column(name="short_description", type="string", length=255, nullable=true)
     * @var string
     */
    private $shortDescription;

    /**
     * @ORM\Column(name="visible", type="boolean")
     * @var boolean
     */
    private $pinned;
    
    /**
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $expiresAt;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;
    
    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="posts")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;
    
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
    
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setShortDescription(){
        $this->shortDescription = strlen($this->description) > 100 ? 
                                  mb_strimwidth($this->description, 0, 100, '...') : $this->description;
        return $this;
    }

    public function getShortDescription() :? string {
        return $this->shortDescription;
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
    
    public function setPicture($picture) {
        $this->picture = $picture;
        return $this;
    }
    
    public function getPicture() {
        return $this->picture;
    }
    
    public function __toString(){
        return $this->getName();
    }
}
