<?php
namespace Puzzle\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Puzzle\AdminBundle\Traits\Describable;
use Puzzle\MediaBundle\Traits\Pictureable;
use Puzzle\AdminBundle\Traits\Nameable;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;

/**
 * Post
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 * @ORM\Table(name="blog_post")
 * @ORM\Entity(repositoryClass="Puzzle\BlogBundle\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Post
{
    use PrimaryKeyTrait,
        Nameable,
        Describable,
        Pictureable,
        Timestampable,
        Blameable,
        Sluggable
    ;
    
    /**
     * @ORM\Column(name="short_description", type="string", length=255, nullable=true)
     * @var string
     */
    private $shortDescription;

    /**
     * @ORM\Column(name="tags", type="array", nullable=true)
     * @var array
     */
    private $tags;
    
    /**
     * @ORM\Column(name="enable_comments", type="boolean")
     * @var boolean
     */
    private $enableComments;
    
    /**
     * @ORM\Column(name="visible", type="boolean")
     * @var boolean
     */
    private $visible;
    
    /**
     * @ORM\Column(name="is_flash", type="boolean")
     * @var boolean
     */
    private $isFlash;
    
    /**
     * @ORM\Column(name="flashExpiresAt", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $flashExpiresAt;
    
    /**
     * @ORM\Column(name="gallery", type="string", length=255, nullable=true)
     * @var string
     */
    private $gallery;
   
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
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="post")
     */
    private $comments;
    
    /**
     * Constructor
     */
    public function __construct() {
    	$this->comments = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->visible = true;
    }
    
    public function getSluggableFields() {
        return ['name'];
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

    public function setTags($tags) : self {
        $this->tags = $tags;
        return $this;
    }
   
    public function addTag($tag) : self {
        $this->tags = array_unique(array_merge($this->tags, [$tag]));
    	return $this;
    }
 
    public function removeTag($tag) : self {
    	$this->tags = array_diff($this->tags, [$tag]);
    	return $this;
    }

    public function getTags() {
        return $this->tags;
    }

    public function setEnableComments($enableComments) : self {
        $this->enableComments = $enableComments;
        return $this;
    }

    public function getEnableComments() :? bool {
        return $this->enableComments;
    }
    
    public function setVisible(bool $visible) : self {
        $this->visible = $visible;
        return $this;
    }
    
    public function isVisible() :? bool {
        return $this->visible;
    }
    
    public function setFlash(bool $isFlash) : self {
        $this->isFlash = $isFlash;
        return $this;
    }
    
    public function isFlash() :? bool {
        return $this->isFlash;
    }
    
    public function setFlashExpiresAt(\DateTime $flashExpiresAt = null) : self {
        $this->flashExpiresAt = $flashExpiresAt;
        return $this;
    }
    
    public function getFlashExpiresAt() :? \DateTime {
        return $this->flashExpiresAt;
    }
    
    public function setGallery(string $gallery) : self {
        $this->gallery = $gallery;
        return $this;
    }
    
    public function getGallery() :?string {
        return $this->gallery;
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

    public function addComment(Comment $comment) : self {
        if ($this->comments === null || $this->comments->contains($comment) === false) {
            $this->comments->add($comment);
        }
        
        return $this;
    }

    public function removeComment(Comment $comment) : self {
        $this->comments->removeElement($comment);
    }

    public function getComments() :? Collection {
        return $this->comments;
    }
    
    public function __toString(){
        return $this->getName();
    }
}
