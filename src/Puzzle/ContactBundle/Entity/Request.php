<?php

namespace Puzzle\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Request
 *
 * @ORM\Table(name="contact_request")
 * @ORM\Entity(repositoryClass="Puzzle\ContactBundle\Repository\RequestRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Request
{
    use PrimaryKeyTrait, Timestampable;
    
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
     * @ORM\ManyToOne(targetEntity="Contact", inversedBy="requests")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     */
    private $contact;
    
    public function __construct() {
        $this->markAsRead = false;
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
    
    public function setContact(Contact $contact) :self {
        $this->contact = $contact;
        return $this;
    }
    
    public function getContact() :?Contact {
        return $this->contact;
    }
    
    public function __toString() {
        return trim($this->lastName. ' '. $this->firstName);
    }
    
    public function fullName() {
        return trim($this->lastName. ' '. $this->firstName);
    }
}
