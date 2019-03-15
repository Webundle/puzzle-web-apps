<?php

namespace Puzzle\CurriculumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Curriculum Training
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 * @ORM\Table(name="curriculum_training")
 * @ORM\Entity(repositoryClass="Puzzle\CurriculumBundle\Repository\TrainingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Training
{
    use PrimaryKeyTrait, Timestampable;
    
    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    private $description;
    
    /**
     * @ORM\Column(name="started_at", type="date")
     * @var \DateTime
     */
    private $startedAt;
    
    /**
     * @ORM\Column(name="ended_at", type="date", nullable=true)
     * @var \DateTime
     */
    private $endedAt;
    
    /**
     * @ORM\Column(name="school", type="string", nullable=true)
     * @var string
     */
    private $school;
    
    /**
     * @ORM\Column(name="address", type="string", nullable=true)
     * @var string
     */
    private $address;
    
    /**
     * @ORM\ManyToOne(targetEntity="Applicant", inversedBy="trainings")
     * @ORM\JoinColumn(name="applicant_id", referencedColumnName="id")
     */
    private $applicant;
    
    public function setName($name) :self {
        $this->name = $name;
        return $this;
    }
    
    public function getName() :?string {
        return $this->name;
    }
    
    public function setDescription($description) :self {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription() :?string {
        return $this->description;
    }
    
    public function setStartedAt(\DateTime $startedAt) :self {
        $this->startedAt = $startedAt;
        return $this;
    }
    
    public function getStartedAt() :?\DateTime {
        return $this->startedAt;
    }
    
    public function setEndedAt(\DateTime $endedAt) :self {
        $this->endedAt = $endedAt;
        return $this;
    }
    
    public function getEndedAt() :?\DateTime {
        return $this->endedAt;
    }
    
    public function setSchool(string $school) :self {
        $this->school = $school;
        return $this;
    }
    
    public function getSchool() :?string {
        return $this->school;
    }
    
    public function setAddress(string $address) :self {
        $this->address = $address;
        return $this;
    }
    
    public function getAddress() :?string {
        return $this->address;
    }
    
    public function setApplicant(Applicant $applicant) : self {
        $this->applicant = $applicant;
        return $this;
    }
    
    public function getApplicant() :?Applicant {
        return $this->applicant;
    }
}
