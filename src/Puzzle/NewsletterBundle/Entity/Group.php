<?php
namespace Puzzle\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Tree\Node;
use Knp\DoctrineBehaviors\Model\Tree\NodeInterface;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;

/**
 * Newsletter Category
 *
 * @author qwincy <cecenho55@gmail.com>
 *
 * @ORM\Table(name="newsletter_group")
 * @ORM\Entity(repositoryClass="Puzzle\NewsletterBundle\Repository\GroupRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Group implements NodeInterface
{
    use PrimaryKeyTrait,
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
     * @ORM\Column(name="name", type="string", length=255)
     * @var string
     */
    private $name;
    
    /**
     * @ORM\ManyToMany(targetEntity="Subscriber", mappedBy="groups")
     */
    private $subscribers;
    
    /**
     * @ORM\OneToMany(targetEntity="Group", mappedBy="parentNode")
     */
    private $childNodes;
    
    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="childNodes")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parentNode;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->subscribers = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getSluggableFields() {
        return ['name'];
    }
    
    public function setName($name) :self {
        $this->name = $name;
        return $this;
    }
    
    public function getName() :?string {
        return $this->name;
    }
    
    
    public function setSubscribers(Collection $subscribers) : self {
        $this->subscribers = $subscribers;
        return $this;
    }
    
    public function addSubscriber(Subscriber $subscriber) : self {
        if ($this->subscribers === null || $this->subscribers->contains($subscriber) === false){
            $this->subscribers->add($subscriber);
        }
        
        return $this;
    }
    
    public function removeSubscriber(Subscriber $subscriber) : self {
        $this->subscribers->removeElement($subscriber);
        return $this;
    }
    
    public function getSubscribers() :? Collection {
        return $this->subscribers;
    }
}
