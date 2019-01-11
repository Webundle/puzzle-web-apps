<?php

namespace Puzzle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\AdminBundle\Traits\Describable;

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
     * @var string
     *
     * @ORM\Column(name="users", type="simple_array", nullable=true)
     */
    private $users;

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setUsers($users) {
    	$this->users = $users;
    	return $this;
    }
    
    public function addUser($user) {
    	$this->users = array_unique(array_merge($this->users, [$user]));
    	return $this;
    }
    
    public function removeUser($user) {
    	$this->users = array_diff($this->users, [$user]);
    	
    	return $this;
    }

    public function getUsers() {
    	return $this->users;
    }
}
