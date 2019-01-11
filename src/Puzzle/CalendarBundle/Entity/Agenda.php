<?php

namespace Puzzle\CalendarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Puzzle\UserBundle\Entity\User;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\AdminBundle\Traits\Nameable;
use Puzzle\AdminBundle\Traits\SlugTrait;
use Puzzle\AdminBundle\Traits\Describable;

/**
 * Agenda
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 * @ORM\Table(name="calendar_agenda")
 * @ORM\Entity(repositoryClass="Puzzle\CalendarBundle\Repository\AgendaRepository")
 */
class Agenda
{
    use PrimaryKeyTrait, 
        Nameable,
        SlugTrait,
        Describable;
    
    /**
     * @ORM\Column(name="visibility", type="string", nullable=true)
     * @var string
     */
    private $visibility;
    
    /**
     * @ORM\OneToMany(targetEntity="Moment", mappedBy="agenda")
     */
    private $moments;
    
    /**
     * @ORM\ManyToOne(targetEntity="Puzzle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @ORM\ManyToMany(targetEntity="Puzzle\UserBundle\Entity\User")
     * @ORM\JoinTable(name="calendar_agenda_member",
     * 		joinColumns={
     * 			@ORM\JoinColumn(name="agenda_id", referencedColumnName="id")},
     * 		inverseJoinColumns={
     * 			@ORM\JoinColumn(name="member_id", referencedColumnName="id")}
     * )
     */
    private $members;
    
    
    /**
     * Constructor
     */
    public function __construct(){
        $this->moments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->members = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId() :? string {
        return $this->id;
    }

    public function setName($name) : self {
        $this->name = $name;
        return $this;
    }
    
    public function getName() :? string {
        return $this->name;
    }

    public function addMoment(Moment $moment) : self {
        if ($this->moments === null || $this->moments !== null && ! $this->moments->contains($moment)) {
            $this->moments[] = $moment;
        }
    	
    	return $this;
    }
    
    public function removeMoment(Moment $moment) : self {
        $this->moments->removeElement($moment);
        return $this;
    }

    public function getMoments() :? Collection {
        return $this->moments;
    }

    public function addMember(User $member) : self {
        if ($this->members === null || $this->members->contains($member) === false ) {
            $this->members->add($member);
        }

        return $this;
    }

    public function removeMember(User $member) : self {
        $this->members->removeElement($member);
        return $this;
    }
    
    public function getMembers() :? Collection {
        return $this->members;
    }

    public function setVisibility($visibility) : self {
        $this->visibility = $visibility;
        return $this;
    }

    public function getVisibility(){
        return $this->visibility;
    }

    public function setUser(User $user) : self {
        $this->user = $user;
        return $this;
    }

    public function getUser() :? User {
        return $this->user;
    }
}
