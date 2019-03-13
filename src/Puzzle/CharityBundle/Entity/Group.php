<?php

namespace Puzzle\CharityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Group
 *
 * @ORM\Table(name="charity_group")
 * @ORM\Entity(repositoryClass="Puzzle\CharityBundle\Repository\GroupRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Group
{
    use PrimaryKeyTrait, Sluggable, Timestampable;
    
    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    private $description;
    
    /**
     * @ORM\OneToMany(targetEntity="Member", mappedBy="group")
     */
    private $members;
    
    public function __construct() {
        $this->members = new \Doctrine\Common\Collections\ArrayCollection();
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
    
    public function setDescription($description) :self {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription() :?string {
        return $this->description;
    }
    
    public function setMembers (Collection $members) :self {
        foreach ($members as $member) {
            $this->addMember($member);
        }
        
        return $this;
    }
    
    public function addMember(Member $member) :self {
        if ($this->members == null || $this->members->contains($member) === false) {
            $member->setGroup($this);
            $this->members->add($member);
        }
        
        return $this;
    }
    
    public function removeMember(Member $member) :self {
        if ($this->members->contains($member) === true) {
            $this->members->removeElement($member);
        }
        
        return $this;
    }
    
    public function getMembers() :?Collection {
        return $this->members;
    }
}
