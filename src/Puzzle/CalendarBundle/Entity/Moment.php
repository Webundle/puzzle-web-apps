<?php

namespace Puzzle\CalendarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Entity\User;
use Doctrine\Common\Collections\Collection;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;

/**
 * Moment
 *
 * @ORM\Table(name="calendar_moment")
 * @ORM\Entity(repositoryClass="Puzzle\CalendarBundle\Repository\MomentRepository")
 */
class Moment
{
    use PrimaryKeyTrait,
        Sluggable,
        Timestampable,
        Blameable
    ;
    
    /**
     * @ORM\Column(name="title", type="string", length=255)
     * @var string
     */
    private $title;
    
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    private $description;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="started_at", type="datetime", nullable=true)
     */
    private $startedAt;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="ended_at", type="datetime", nullable=true)
     */
    private $endedAt;
    
    /**
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     * @var string
     */
    private $location;
    
    /**
     * @ORM\Column(name="color", type="string", length=255, nullable=true)
     * @var string
     */
    private $color;
    
    /**
     * @ORM\Column(name="tags", type="array", nullable=true)
     * @var array
     */
    private $tags;
    
    /**
     * @ORM\Column(name="enable_comments", type="boolean")
     * @var boolean
     */
    private $enableComments;
    
    /**
     * @ORM\Column(name="visibility", type="string", nullable=true)
     * @var string
     */
    private $visibility;
    
    /**
     * @ORM\Column(name="picture", type="string", nullable=true)
     * @var string
     */
    private $picture;
    
    /**
     * @ORM\Column(name="is_recurrent", type="boolean")
     * @var string
     */
    private $isRecurrent;
    
    /**
     * @ORM\ManyToOne(targetEntity="Agenda", inversedBy="moments")
     * @ORM\JoinColumn(name="agenda_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $agenda;
    
    /**
     * @ORM\ManyToOne(targetEntity="Puzzle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @ORM\ManyToMany(targetEntity="Puzzle\UserBundle\Entity\User")
     * @ORM\JoinTable(name="calendar_moment_member",
     * 		joinColumns={
     * 			@ORM\JoinColumn(name="moment_id", referencedColumnName="id")},
     * 		inverseJoinColumns={
     * 			@ORM\JoinColumn(name="member_id", referencedColumnName="id")}
     * )
     */
    private $members;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->members = new \Doctrine\Common\Collections\ArrayCollection();
        $this->enableComments = false;
    }
    
    public function getSluggableFields() {
        return ['title'];
    }
    
    public function setTitle (string $title) :self {
        $this->title = $title;
        return $this;
    }
    
    public function getTitle() :? string {
        return $this->title;
    }
    
    public function setDescription($description) :self {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription() :?string {
        return $this->description;
    }
    
    public function setStartedAt (\DateTime $startedAt) :self {
        $this->startedAt = $startedAt;
        return $this;
    }
    
    public function getStartedAt() :? \DateTime {
        return $this->startedAt;
    }
    
    public function setEndedAt(\DateTime $endedAt) :self {
        $this->endedAt = $endedAt;
        return $this;
    }
    
    public function getEndedAt() :?\DateTime {
        return $this->endedAt;
    }
    
    public function setLocation(string $location) :self {
        $this->location = $location;
        return $this;
    }
    
    public function getLocation() :?string {
        return $this->location;
    }
    
    public function setColor($color) :self {
        $this->color = $color;
        return $this;
    }
    
    public function getColor() :?string {
        return $this->color;
    }
    
    public function setTags($tags) :self {
        $this->tags = $tags;
        return $this;
    }
    
    public function addTag($tag) :self {
        $this->tags[] = $tag;
        $this->tags = array_unique($this->tags);
        return $this;
    }
    
    public function removeTag($tag) :self {
        $this->tags = array_diff($this->tags, [$tag]);
        return $this;
    }
    
    public function getTags() {
        return $this->tags;
    }
    
    public function setEnableComments(bool $enableComments) :self {
        $this->enableComments = $enableComments;
        return $this;
    }
    
    public function getEnableComments() :?bool {
        return $this->enableComments;
    }
    
    public function setPicture(string $picture = null) :self {
        $this->picture = $picture;
        return $this;
    }
    
    public function getPicture() :?string {
        return $this->picture;
    }
    
    public function setAgenda(Agenda $agenda) :self {
        $this->agenda = $agenda;
        return $this;
    }
    
    public function getAgenda() :?Agenda {
        return $this->agenda;
    }
    
    public function setMembers(Collection $members) :self {
        $this->members = $members;
    }
    
    public function addMember(User $member) :self {
        if(! $this->getMembers() || $this->getMembers() && ! $this->getMembers()->contains($member)){
            $this->members[] = $member;
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
    
    public function setIsRecurrent(bool $isRecurrent) :self {
        $this->isRecurrent = $isRecurrent;
        return $this;
    }
    
    public function isRecurrent() :?bool {
        return $this->isRecurrent;
    }
    
    public function setVisibility(string $visibility) :self {
        $this->visibility = $visibility;
        return $this;
    }
    
    public function getVisibility() :?string {
        return $this->visibility;
    }
    
    
    public function addComment(Comment $comment) : self {
        if ($this->comments === null || $this->comments->contains($comment) === false) {
            $this->comments->add($comment);
        }
        
        return $this;
    }
    
    public function removeComment(Comment $comment) : self {
        $this->comments->removeElement($comment);
    }
    
    public function getComments() :? Collection {
        return $this->comments;
    }
}
