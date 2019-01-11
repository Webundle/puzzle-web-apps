<?php

namespace Puzzle\ExpertiseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\MediaBundle\Traits\Pictureable;
use Puzzle\AdminBundle\Traits\DatetimeTrait;
use Puzzle\AdminBundle\Traits\Describable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;

/**
 * Staff
 *
 * @ORM\Table(name="expertise_staff")
 * @ORM\Entity(repositoryClass="Puzzle\ExpertiseBundle\Repository\StaffRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Staff
{
    use PrimaryKeyTrait,
        Pictureable,
        DatetimeTrait,
        Describable,
        Blameable;
    
    /**
     * @ORM\Column(name="first_name", type="string", length=255)
     * @var string
     */
    private $firstName;
    
    /**
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     * @var string
     */
    private $lastName;
    
    /**
     * @ORM\Column(name="position", type="string", length=255)
     * @var string
     */
    private $position;
    
    /**
     * @ORM\Column(name="phoneNumber", type="string", length=255, nullable=true, unique=true)
     * @var string
     */
    private $phoneNumber;
    
    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=true, unique=true)
     * @var string
     */
    private $email;
    
    /**
     * @ORM\Column(name="facebook", type="string", length=255, nullable=true, unique=true)
     * @var string
     */
    private $facebook;
    
    /**
     * @ORM\Column(name="twitter", type="string", length=255, nullable=true, unique=true)
     * @var string
     */
    private $twitter;
    
    /**
     * @ORM\Column(name="linkedIn", type="string", length=255, nullable=true, unique=true)
     * @var string
     */
    private $linkedIn;
    
    /**
     * @ORM\Column(name="googlePlus", type="string", length=255, nullable=true, unique=true)
     * @var string
     */
    private $googlePlus;
    
    /**
     * @ORM\Column(name="ranking", type="integer")
     * @var integer
     */
    private $ranking;
    
    /**
     * @ORM\ManyToOne(targetEntity="Service", inversedBy="staffs")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     */
    private $service;
    
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
    
    public function setService(Service $service = null) : self {
        $this->service = $service;
        return $this;
    }
    
    public function getService() :? Service {
        return $this->service;
    }

    public function setPosition($position) : self {
        $this->position = $position;
        return $this;
    }

    public function getPosition() :? string {
        return $this->position;
    }

    public function setAddress($address) : self {
        $this->address = $address;
        return $this;
    }

    public function getAddress() :? string {
        return $this->address;
    }

    public function setPhoneNumber($phoneNumber) : self {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getPhoneNumber() :? string {
        return $this->phoneNumber;
    }

    public function setEmail($email) : self {
        $this->email = $email;
        return $this;
    }

    public function getEmail() :? string {
        return $this->email;
    }
    
    public function setRanking($ranking) : self {
        $this->ranking = $ranking;
        return $this;
    }
    
    public function getRanking():? int {
        return $this->ranking;
    }
    
    public function setFacebook(string $facebook = null) :self {
        $this->facebook = $facebook;
        return $this;
    }
    
    public function getFacebook() :?string {
        return $this->facebook;
    }
    
    public function setTwitter(string $twitter = null) :self {
        $this->twitter = $twitter;
        return $this;
    }
    
    public function getTwitter() :?string {
        return $this->twitter;
    }
    
    public function setLinkedIn(string $linkedIn = null) :self {
        $this->linkedIn = $linkedIn;
        return $this;
    }
    
    public function getLinkedIn() :?string {
        return $this->linkedIn;
    }
    
    public function setGooglePlus(string $googlePlus = null) :self {
        $this->googlePlus = $googlePlus;
        return $this;
    }
    
    public function getGooglePlus() :?string {
        return $this->googlePlus;
    }
    
    public function __toString() {
        return $this->lastName.' '.$this->firstName;
    }
}
