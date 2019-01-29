<?php

namespace Puzzle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\AdminBundle\Traits\Describable;
use Doctrine\Common\Collections\Collection;

/**
 * Group
 *
 * @ORM\Table(name="user_group")
 * @ORM\Entity(repositoryClass="Puzzle\UserBundle\Repository\GroupRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Group
{
	use PrimaryKeyTrait,
	    Describable;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * 
     * @ORM\ManyToMany(targetEntity="User", inversedBy="groups")
     * @ORM\JoinTable(name="user_groups",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    private $users;
    
    public function __construct() {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setUsers (Collection $users) : self {
        foreach ($users as $user) {
            $this->addUser($user);
        }
        
        return $this;
    }
    
    public function addUser(User $user) :self {
        if ($this->users->count() === 0 || $this->users->contains($user) === false) {
            $this->users->add($user);
            $user->addGroup($this);
        }
        
        return $this;
    }
    
    public function removeUser(User $user) :self {
        if ($this->users->contains($user) === true) {
            $this->users->removeElement($user);
        }
        
        return $this;
    }
    
    public function getUsers() :?Collection {
        return $this->users;
    }
}
