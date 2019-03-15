<?php

namespace Puzzle\CurriculumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\AdminBundle\Traits\DatetimeTrait;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Doctrine\Common\Collections\Collection;
use Puzzle\UserBundle\Entity\User;

/**
 * Curriculum Applicant
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 * @ORM\Table(name="curriculum_applicant")
 * @ORM\Entity(repositoryClass="Puzzle\CurriculumBundle\Repository\ApplicantRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Applicant
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
     * @ORM\Column(name="birthday", type="date")
     */
    private $birthday;
    
    /**
     * @var string
     * @ORM\Column(name="biography", type="string", length=255, nullable=true)
     */
    private $biography;

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
     * @var string
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;
    
    /**
     * @ORM\Column(name="address", type="string", nullable=true)
     * @var string
     */
    private $address;
    
    /**
     * @ORM\Column(name="single", type="boolean")
     * @var bool
     */
    private $single;
    
    /**
     * @ORM\Column(name="child_count", type="integer")
     * @var int
     */
    private $childCount;
    
    /**
     * @ORM\Column(name="picture", type="string", nullable=true)
     * @var string
     */
    private $picture;
    
    /**
     * @ORM\Column(name="file", type="string", nullable=true)
     * @var string
     */
    private $file;
    
    /**
     * @ORM\Column(name="skills", type="array", nullable=true)
     * @var array
     */
    private $skills;
    
    /**
     * @ORM\Column(name="hobbies", type="array", nullable=true)
     * @var array
     */
    private $hobbies;
    
    /**
     * @ORM\OneToMany(targetEntity="Work", mappedBy="applicant")
     */
    private $works;
    
    /**
     * @ORM\OneToMany(targetEntity="Training", mappedBy="applicant")
     */
    private $trainings;
    
    /**
     * @ORM\OneToOne(targetEntity="Puzzle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    public function __construct() {
        $this->trainings = new \Doctrine\Common\Collections\ArrayCollection();
        $this->works = new \Doctrine\Common\Collections\ArrayCollection();
        $this->single = false;
        $this->childCount = 0;
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
    
    public function setBirthday(\DateTime $birthday) :self {
        $this->birthday = $birthday;
        return $this;
    }
    
    public function getBirthday() :?\DateTime {
        return $this->birthday;
    }
    
    public function setBiography(string $biography) :self {
        $this->biography = $biography;
        return $this;
    }
    
    public function getBiography() :?string {
        return $this->biography;
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
    
    public function setWebsite(string $website) :self {
        $this->website = $website;
        return $this;
    }
    
    public function getWebsite() :?string {
        return $this->website;
    }
    
    public function setAddress(string $address) :self {
        $this->address = $address;
        return $this;
    }
    
    public function getAddress() :?string {
        return $this->address;
    }
    
    public function setSingle(bool $single) :self {
        $this->single = $single;
        return $this;
    }
    
    public function isSingle() :?bool {
        return $this->single;
    }
    
    public function setChildCount(int $childCount) :self {
        $this->childCount = $childCount;
        return $this;
    }
    
    public function getChildCount() :?int {
        return $this->childCount;
    }
    
    public function setPicture(string $picture) :self {
        $this->picture = $picture;
        return $this;
    }
    
    public function getPicture() :?string {
        return $this->picture;
    }
    
    public function setFile(string $file) :self {
        $this->file = $file;
        return $this;
    }
    
    public function getFile() :?string {
        return $this->file;
    }
    
    public function setUser(User $user) :self {
        $this->user = $user;
        return $this;
    }
    
    public function getUser() :?User {
        return $this->user;
    }
    
    public function setSkills($skills) : self {
        $this->skills = $skills;
        return $this;
    }
    
    public function addSkill($skill) : self {
        $this->skills = array_unique(array_merge($this->skills, [$skill]));
        return $this;
    }
    
    public function removeSkill($skill) : self {
        $this->skills = array_diff($this->skills, [$skill]);
        return $this;
    }
    
    public function getSkills() {
        return $this->skills;
    }
    
    public function setHobbies($hobbies) : self {
        $this->hobbies = $hobbies;
        return $this;
    }
    
    public function addHobby($hobby) : self {
        $this->hobbies = array_unique(array_merge($this->hobbies, [$hobby]));
        return $this;
    }
    
    public function removeHobby($hobby) : self {
        $this->hobbies = array_diff($this->hobbies, [$hobby]);
        return $this;
    }
    
    public function getHobbies() {
        return $this->hobbies;
    }
    
    public function setWorks(Collection $works) :self {
        foreach($works as $work) {
            $this->addWork($work);
        }
        
        return $this;
    }
    
    public function addWork(Work $work) : self {
        if ($this->works === null || $this->works->contains($work) === false){
            $this->works->add($work);
        }
        
        return $this;
    }
    
    public function removeWork(Work $work) : self {
        $this->works->removeElement($work);
    }
    
    public function getWorks() :? Collection {
        return $this->works;
    }
    
    public function setTrainings(Collection $trainings) :self {
        foreach($trainings as $training) {
            $this->addTraining($training);
        }
        
        return $this;
    }
    
    public function addTraining(Training $training) : self {
        if ($this->trainings === null || $this->trainings->contains($training) === false){
            $this->trainings->add($training);
        }
        
        return $this;
    }
    
    public function removeTraining(Training $training) : self {
        $this->works->removeElement($training);
    }
    
    public function getTrainings() :? Collection {
        return $this->trainings;
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
