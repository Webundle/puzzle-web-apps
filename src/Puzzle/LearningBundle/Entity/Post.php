<?php
namespace Puzzle\LearningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\AdminBundle\Traits\Nameable;
use Puzzle\AdminBundle\Traits\Describable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Puzzle\MediaBundle\Traits\Pictureable;
use Doctrine\Common\Collections\Collection;
use Puzzle\UserBundle\Entity\User;

/**
 * Post
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 * @ORM\Table(name="learning_post")
 * @ORM\Entity(repositoryClass="Puzzle\LearningBundle\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Post
{
    use PrimaryKeyTrait,
        Nameable,
        Describable,
        Timestampable,
        Sluggable,
        Pictureable,
        Blameable;
    
    /**
     * @var boolean
     * @ORM\Column(name="enable_comments", type="boolean")
     */
    private $enableComments;
    
    /**
     * @ORM\Column(name="visible", type="boolean")
     * @var boolean
     */
    private $visible;
    
    /**
     * @ORM\Column(name="tags", type="array", nullable=true)
     * @var array
     */
    private $tags;

    /**
     * @var string
     * @ORM\Column(name="video", type="string", nullable=true)
     */
    private $video;
    
    /**
     * @var string
     * @ORM\Column(name="audio", type="string", nullable=true)
     */
    private $audio;
    
    /**
     * @var string
     * @ORM\Column(name="document", type="string", nullable=true)
     */
    private $document;
    
    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="posts")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;
    
    /**
     * @ORM\ManyToOne(targetEntity="Puzzle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="speaker_id", referencedColumnName="id")
     */
    private $speaker;
    
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="post")
     */
    private $comments;
    
    /**
     * Constructor
     */
    public function __construct() {
    	$this->comments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getSluggableFields() {
        return ['name'];
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

    public function setSpeaker(User  $speaker) : self {
        $this->speaker = $speaker;
        return $this;
    }

    public function getSpeaker() :? User {
        return $this->speaker;
    }

    public function setVideo($video) : self {
        $this->video = $video;
        return $this;
    }

    public function getVideo() :? string {
        return $this->video;
    }

    public function setAudio($audio) : self {
        $this->audio = $audio;
        return $this;
    }

    public function getAudio() :? string {
        return $this->audio;
    }

    public function setDocument($document) : self {
        $this->document = $document;
        return $this;
    }

    public function getDocument() :? string {
        return $this->document;
    }
}
