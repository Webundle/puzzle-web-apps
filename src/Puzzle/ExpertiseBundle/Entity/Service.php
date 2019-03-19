<?php

namespace Puzzle\ExpertiseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\AdminBundle\Traits\DatetimeTrait;
use Doctrine\Common\Collections\Collection;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\MediaBundle\Traits\Pictureable;
use Puzzle\UserBundle\Traits\UserTrait;
use Puzzle\AdminBundle\Traits\Nameable;
use Puzzle\AdminBundle\Traits\Describable;
use Puzzle\AdminBundle\Traits\SlugTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;

/**
 * Service
 *
 * @ORM\Table(name="expertise_service")
 * @ORM\Entity(repositoryClass="Puzzle\ExpertiseBundle\Repository\ServiceRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Service
{
    use PrimaryKeyTrait,
        Nameable,
        Describable,
        Pictureable, 
        Timestampable, 
        Blameable,
        Sluggable;
    
    
    /**
     * @ORM\Column(name="class_icon", type="string", length=255, nullable=true)
     * @var string
     */
    private $classIcon;

    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="service")
     */
    private $projects;
    
    /**
     * @ORM\OneToMany(targetEntity="Staff", mappedBy="service")
     */
    private $staffs;
    
    /**
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="service")
     */
    private $contacts ;
    
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->projects = new \Doctrine\Common\Collections\ArrayCollection();
        $this->contacts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->staffs = new \Doctrine\Common\Collections\ArrayCollection();
    }
	
    public function getSluggableFields() {
        return ['name'];
    }
    
    public function addProject(Project $project) : self {
        if ($this->projects === null ||$this->projects->contains($project) === false) {
            $this->projects->add($project);
        }

        return $this;
    }

    public function removeProject(Project $project) : self {
        $this->projects->removeElement($project);
    }

    public function getProjects() :? Collection {
        return $this->projects;
    }

    public function setClassIcon($classIcon) : self {
        $this->classIcon = $classIcon;
        return $this;
    }

    public function getClassIcon() :? string {
        return $this->classIcon;
    }
    
    public function setStaffs(Collection $staffs = null) {
        $this->staffs = $staffs;
        return $this;
    }
    
    public function addStaff(Staff $staff) {
        if ($this->staffs === null || $this->staffs->contains($staff) === false) {
            $this->staffs->add($staff);
        }
        
        return $this;
    }
    
    public function removeStaff(Staff $staff) {
        $this->staffs->removeElement($staff);
        return $this;
    }
    
    public function getStaffs() {
        return $this->staffs;
    }
    
    public function setContacts (Collection $contacts) :self {
        foreach ($contacts as $contact) {
            $this->addContact($contact);
        }
        
        return $this;
    }
    
    public function addContact(Contact $contact) :self {
        if ($this->contacts->count() === 0 || $this->contacts->contains($contact) === false) {
            $this->contacts->add($contact);
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
    
}
