<?php

namespace Puzzle\CalendarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Puzzle\UserBundle\Entity\User;

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
        Sluggable,
        Timestampable,
        Blameable
    ;
    
    /**
     * @ORM\Column(name="name", type="string", length=255)
     * @var string
     */
    private $name;
    
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    private $description;
    
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
    
    
    public function getSluggableFields() {
        return ['name'];
    }

    public function setName($name) : self {
        $this->name = $name;
        return $this;
    }
    
    public function getName() :? string {
        return $this->name;
    }
    
    public function setDescription($description) :self {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription() :? string {
        return $this->description;
    }
    
    public function setVisibility($visibility) : self {
        $this->visibility = $visibility;
        return $this;
    }
    
    public function getVisibility(){
        return $this->visibility;
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

    public function removeMember(User $member) :self {
        $this->members->removeElement($member);
        return $this;
    }
    
    public function getMembers() :?Collection {
        return $this->members;
    }
}
