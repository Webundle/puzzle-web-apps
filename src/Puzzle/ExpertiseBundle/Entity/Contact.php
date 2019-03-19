<?php

namespace Puzzle\ExpertiseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;

/**
 * Request
 *
 * @ORM\Table(name="expertise_contact")
 * @ORM\Entity(repositoryClass="Puzzle\ExpertiseBundle\Repository\ContactRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Contact
{
    use PrimaryKeyTrait, Timestampable, Blameable;
    
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
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;
    
    /**
     * @var string
     * @ORM\Column(name="phoneNumber", type="string", length=255, nullable=true)
     */
    private $phoneNumber;
    
    /**
     * @var string
     * @ORM\Column(name="subject", type="string", length=255, nullable=true)
     */
    private $subject;
    
    /**
     * @var string
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;
    
    /**
     * @var string
     * @ORM\Column(name="mark_as_read", type="boolean")
     */
    private $markAsRead;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="read_at", type="datetime", nullable=true)
     */
    private $readAt;
    
    /**
     * @ORM\ManyToOne(targetEntity="Service", inversedBy="contacts")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     */
    private $service;
    
    public function __construct() {
        $this->markAsRead = false;
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
    
    public function setSubject($subject) :self {
        $this->subject = $subject;
        return $this;
    }
    
    public function getSubject() :?string {
        return $this->subject;
    }
    
    public function setMessage($message) :self {
        $this->message = $message;
        return $this;
    }
    
    public function getMessage() :?string {
        return $this->message;
    }
    
    public function markAsRead() :self {
        $this->markAsRead = true;
        return $this;
    }
    
    public function isMarkedAsRead() :?bool {
        return $this->markAsRead;
    }
    
    public function setReadAt(\DateTime $readAt) :self {
        $this->readAt = $readAt;
        return $this;
    }
    
    public function getReadAt() :?\DateTime {
        return $this->readAt;
    }
     
    public function setService(Service $service) :self {
        $this->service = $service;
        return $this;
    }
    
    public function getService() :?Service {
        return $this->service;
    }
    
    public function __toString() {
        return trim($this->lastName. ' '. $this->firstName);
    }
    
    public function fullName() {
        return trim($this->lastName. ' '. $this->firstName);
    }
}
