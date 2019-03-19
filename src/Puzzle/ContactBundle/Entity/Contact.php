<?php

namespace Puzzle\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\AdminBundle\Traits\DatetimeTrait;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Doctrine\Common\Collections\Collection;
use Puzzle\UserBundle\Entity\User;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;

/**
 * Contact
 *
 * @ORM\Table(name="contact_contact")
 * @ORM\Entity(repositoryClass="Puzzle\ContactBundle\Repository\ContactRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Contact
{
    use PrimaryKeyTrait, Timestampable, Blameable;
    
    /**
     * @var string
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $firstName;
    
    /**
     * @var string
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;
    
    /**
     * @var string
     * @ORM\Column(name="phoneNumber", type="string", length=255, nullable=true)
     */
    private $phoneNumber;
    
    /**
     * @var string
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    private $location;
    
    /**
     * @var string
     * @ORM\Column(name="picture", type="string", length=255, nullable=true)
     */
    private $picture;
    
    /**
     * @var string
     * @ORM\Column(name="company", type="string", length=255, nullable=true)
     */
    private $company;
    
    /**
     * @var string
     * @ORM\Column(name="position", type="string", length=255, nullable=true)
     */
    private $position;
    
    /**
     * @ORM\OneToOne(targetEntity="Puzzle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @ORM\ManyToMany(targetEntity="Group", mappedBy="contacts")
     */
    private $groups;
    
    
    public function __construct() {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function setEmail($email) : self {
        $this->email = $email;
        return $this;
    }

    public function getEmail() :? string {
        return $this->email;
    }
    
    public function setPhoneNumber($phoneNumber) : self {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getPhoneNumber() :? string {
        return $this->phoneNumber;
    }

    public function setLocation($location) : self {
        $this->location = $location;
        return $this;
    }

    public function getLocation() :? string {
        return $this->location;
    }

    public function setCompany($company) : self {
        $this->company = $company;
        return $this;
    }

    public function getCompany() :? string {
        return $this->company;
    }

    public function setPosition($position) : self {
        $this->position = $position;
        return $this;
    }

    public function getPosition() :?string {
        return $this->position;
    }

    public function setFirstName($firstName) : self {
        $this->firstName = $firstName;
        return $this;
    }
    
    public function getFirstName() :? string {
        return $this->firstName;
    }

    public function setLastName($lastName) : self {
        $this->lastName = $lastName;
        return $this;
    }

    public function getLastName() :? string {
        return $this->lastName;
    }

    public function setPicture($picture) :? string {
        $this->picture = $picture;
        return $this;
    }

    public function getPicture() :? string {
        return $this->picture;
    }
    
    public function setUser(User $user) :self {
        $this->user = $user;
        return $this;
    }
    
    public function getUser() :?User {
        return $this->user;
    }
    
    public function setGroups (Collection $groups) :self {
        foreach ($groups as $group) {
            $this->addGroup($group);
        }
        
        return $this;
    }
    
    public function addGroup(Group $group) :self {
        if ($this->groups->count() === 0 || $this->groups->contains($group) === false) {
            $this->groups->add($group);
        }
        
        return $this;
    }
    
    public function removeGroup(Group $group) :self {
        if ($this->groups->contains($group) === true) {
            $this->groups->removeElement($group);
        }
        
        return $this;
    }
    
    public function getGroups() :?Collection {
        return $this->groups;
    }
    
    public function __toString() {
        return trim($this->lastName. ' '. $this->firstName);
    }
    
    public function fullName() {
        return trim($this->lastName. ' '. $this->firstName);
    }
}
