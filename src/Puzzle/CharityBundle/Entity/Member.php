<?php

namespace Puzzle\CharityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\AdminBundle\Traits\DatetimeTrait;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Doctrine\Common\Collections\Collection;
use Puzzle\UserBundle\Entity\User;

/**
 * Charity Member
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 * @ORM\Table(name="charity_member")
 * @ORM\Entity(repositoryClass="Puzzle\CharityBundle\Repository\MemberRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Member
{
    use PrimaryKeyTrait, Timestampable;
    
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
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;
    
    /**
     * @var string
     * @ORM\Column(name="phoneNumber", type="string", length=255, nullable=true)
     */
    private $phoneNumber;
    
    /**
     * @var bool
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="enabled_at", type="datetime", nullable=true)
     */
    private $enabledAt;
    
    /**
     * @ORM\OneToMany(targetEntity="Donation", mappedBy="member")
     */
    private $donations;
    
    /**
     * @ORM\OneToOne(targetEntity="Puzzle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @var array
     * @ORM\ManyToMany(targetEntity="Group", mappedBy="members")
     */
    private $groups;
    
    public function __construct() {
        $this->donations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->enabled = false;
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
    
    public function setEnabled($enabled) :self {
        $this->enabled = $enabled;
        return $this;
    }
    
    public function isEnabled() :?bool {
        return $this->enabled;
    }
    
    public function setEnabledAt(\DateTime $enabledAt) :self {
        $this->enabledAt = $enabledAt;
        return $this;
    }
    
    public function getEnabledAt() {
        return $this->enabledAt;
    }
    
    public function setUser(User $user) :self {
        $this->user = $user;
        return $this;
    }
    
    public function getUser() :?User {
        return $this->user;
    }
  
    public function addDonation(Donation $donation) : self {
        if ($this->donations === null || $this->donations->contains($donation) === false){
            $this->donations->add($donation);
        }
        
        return $this;
    }
    
    public function removeDonation(Donation $donation) : self {
        $this->donations->removeElement($donation);
    }
    
    public function getDonations() :? Collection {
        return $this->donations;
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
            $group->addUser($this);
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
    
    public function getFullName(int $width = null) :?string {
        $fullName = $this->firstName ?: '';
        $fullName .= $this->lastName && $this->firstName ? ' '.$this->lastName : ($this->lastName ?: '');
        
        return $width && $fullName ? mb_strimwidth($fullName, 0, $width, '...') : $fullName;
    }
    
    public function __toString() {
        return $this->getFullName();
    }
}
