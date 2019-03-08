<?php
namespace Puzzle\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Tree\Node;
use Knp\DoctrineBehaviors\Model\Tree\NodeInterface;
use Puzzle\AdminBundle\Traits\Describable;
use Puzzle\AdminBundle\Traits\Nameable;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;

/**
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 * @ORM\Table(name="contact_group")
 * @ORM\Entity(repositoryClass="Puzzle\ContactBundle\Repository\GroupRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Group implements NodeInterface
{
    use PrimaryKeyTrait,
        Nameable,
        Describable,
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
     * @ORM\OneToMany(targetEntity="Group", mappedBy="parentNode")
     */
    private $childNodes;
    
    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="childNodes")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parentNode;
    
    /**
     * @ORM\ManyToMany(targetEntity="Contact", inversedBy="groups")
     * @ORM\JoinTable(name="contact_groups",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="id")}
     * )
     */
    private $contacts;
    
    public function __construct() {
        $this->contacts = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getSluggableFields() {
        return ['name'];
    }
    
    
    public function setContacts (Collection $contacts) : self {
        foreach ($contacts as $contact) {
            $this->addContact($contact);
        }
        
        return $this;
    }
    
    public function addContact(Contact $contact) :self {
        if ($this->contacts->count() === 0 || $this->contacts->contains($contact) === false) {
            $this->contacts->add($contact);
            $contact->addGroup($this);
        }
        
        return $this;
    }
    
    public function removeContact(Contact $contact) :self {
        if ($this->contacts->contains($contact) === true) {
            $this->contacts->removeElement($contact);
        }
        
        return $this;
    }
    
    public function getContacts() :?Collection {
        return $this->contacts;
    }
    
    public function __toString() {
        return $this->name;
    }
}
