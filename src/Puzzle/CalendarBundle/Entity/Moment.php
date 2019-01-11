<?php

namespace Puzzle\CalendarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Entity\User;
use Doctrine\Common\Collections\Collection;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\AdminBundle\Traits\Describable;
use Puzzle\MediaBundle\Traits\Pictureable;
use Puzzle\AdminBundle\Traits\SlugTrait;
use Puzzle\AdminBundle\Traits\DatetimePeriodTrait;
use Puzzle\AdminBundle\Traits\DatetimeTrait;
use Puzzle\UserBundle\Traits\UserTrait;

/**
 * Moment
 *
 * @ORM\Table(name="calendar_moment")
 * @ORM\Entity(repositoryClass="Puzzle\CalendarBundle\Repository\MomentRepository")
 */
class Moment
{
    use PrimaryKeyTrait,
        Describable,
        Pictureable,
        SlugTrait,
        DatetimePeriodTrait,
        DatetimeTrait,
        UserTrait;
    
    /**
     * @ORM\Column(name="title", type="string", length=255)
     * @var string
     */
    private $title;
    
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
     * @ORM\Column(name="is_recurrent", type="boolean")
     * @var string
     */
    private $isRecurrent;
    
    /**
     * @ORM\ManyToOne(targetEntity="Agenda", inversedBy="moments")
     * @ORM\JoinColumn(name="agenda_id", referencedColumnName="id")
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
    
    public function setTitle(string $title){
        $this->title = $title;
        return $this;
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function setLocation(string $location){
        $this->location = $location;
        return $this;
    }
    
    public function getLocation(){
        return $this->location;
    }
    
    public function setColor($color){
        $this->color = $color;
        return $this;
    }
    
    public function getColor(){
        return $this->color;
    }
    
    public function setTags($tags){
        $this->tags = $tags;
        return $this;
    }
    
    public function addTag($tag){
        $this->tags[] = $tag;
        $this->tags = array_unique($this->tags);
        return $this;
    }
    
    public function removeTag($tag){
        $this->tags = array_diff($this->tags, [$tag]);
        return $this;
    }
    
    public function getTags(){
        return $this->tags;
    }
    
    public function setEnableComments($enableComments){
        $this->enableComments = $enableComments;
        return $this;
    }
    
    public function getEnableComments(){
        return $this->enableComments;
    }
    
    public function setPicture(string $picture = null){
        $this->picture = $picture;
        return $this;
    }
    
    public function getPicture(){
        return $this->picture;
    }
    
    public function setAgenda(Agenda $agenda = null){
        $this->agenda = $agenda;
        return $this;
    }
    
    public function getAgenda(){
        return $this->agenda;
    }
    
    public function setUser(User $user = null){
        $this->user = $user;
        return $this;
    }
    
    public function getUser(){
        return $this->user;
    }
    
    public function setMembers(Collection $members = null) : self {
        $this->members = $members;
    }
    
    public function addMember(User $member){
        if(! $this->getMembers() || $this->getMembers() && ! $this->getMembers()->contains($member)){
            $this->members[] = $member;
        }
        
        return $this;
    }
    
    public function removeMember(User $member){
        $this->members->removeElement($member);
        return $this;
    }
    
    public function getMembers(){
        return $this->members;
    }
    
    public function setIsRecurrent(bool $isRecurrent){
        $this->isRecurrent = $isRecurrent;
        return $this;
    }
    
    public function getIsRecurrent(){
        return $this->isRecurrent;
    }
    
    public function setVisibility(string $visibility){
        $this->visibility = $visibility;
        return $this;
    }
    
    public function getVisibility(){
        return $this->visibility;
    }
}
