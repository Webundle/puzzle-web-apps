<?php

namespace Puzzle\CurriculumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Curriculum Work
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 * @ORM\Table(name="curriculum_work")
 * @ORM\Entity(repositoryClass="Puzzle\CurriculumBundle\Repository\WorkRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Work
{
    use PrimaryKeyTrait, Timestampable;
    
    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var string
     * @ORM\Column(name="position", type="string", length=255)
     */
    private $position;
    
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
     * @ORM\Column(name="company", type="string", nullable=true)
     * @var string
     */
    private $company;
    
    /**
     * @ORM\Column(name="address", type="string", nullable=true)
     * @var string
     */
    private $address;
    
    /**
     * @ORM\ManyToOne(targetEntity="Applicant", inversedBy="works")
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
    
    public function setPosition(string $position) :self {
        $this->position = $position;
        return $this;
    }
    
    public function getPosition() :?string {
        return $this->position;
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
    
    public function setCompany(string $company) :self {
        $this->company = $company;
        return $this;
    }
    
    public function getCompany() :?string {
        return $this->company;
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
