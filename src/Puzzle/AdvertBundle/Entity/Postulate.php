<?php
namespace Puzzle\AdvertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Puzzle\UserBundle\Entity\User;

/**
 * Postulate
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 * @ORM\Table(name="advert_postulate")
 * @ORM\Entity(repositoryClass="Puzzle\AdvertBundle\Repository\PostulateRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Postulate
{
    use PrimaryKeyTrait,
        Timestampable
    ;
    
    /**
     * @ORM\Column(name="confirmed", type="boolean")
     * @var boolean
     */
    private $confirmed;
    
    /**
     * @ORM\Column(name="confirmed_at", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $confirmedAt;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $file;
    
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    private $description;
    
    /**
     * @ORM\ManyToOne(targetEntity="Puzzle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="postulates")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $post;
    
    /**
     * Constructor
     */
    public function __construct() {
    	$this->confirmed = false;
    }
    
    public function setConfirmed(bool $confirmed) : self {
        $this->confirmed = $confirmed;
        return $this;
    }
    
    public function isConfirmed() :? bool {
        return $this->confirmed;
    }
    
    public function setFile($file) {
        $this->file = $file;
        return $this;
    }
    
    public function getFile() {
        return $this->file;
    }
    
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function setUser(User $user) : self {
        $this->user = $user;
        return $this;
    }

    public function getUser() :? User {
        return $this->user;
    }
    
    public function setPost(Post $post) : self {
        $this->post = $post;
        return $this;
    }
    
    public function getPost() :? Post {
        return $this->post;
    }
    
    public function setConfirmedAt(\DateTime $confirmedAt) :self {
        $this->confirmedAt = $confirmedAt;
        return $this;
    }
    
    public function getConfirmedAt() :?\DateTime {
        return $this->confirmedAt;
    }
}
