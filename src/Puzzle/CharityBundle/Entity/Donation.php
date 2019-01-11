<?php

namespace Puzzle\CharityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Doctrine\Common\Collections\Collection;

/**
 * Donation
 *
 * @ORM\Table(name="charity_donation")
 * @ORM\Entity(repositoryClass="Puzzle\CharityBundle\Repository\DonationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Donation
{
    use PrimaryKeyTrait, 
        Timestampable;
    
    /**
     * @var string
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;
    
    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;
    
    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=255)
     */
    private $phone;
    
    /**
     * @var string
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var int
     * @ORM\Column(name="total_amount", type="integer")
     */
    private $totalAmount;

    /**
     * @var int
     * @ORM\Column(name="paid_amount", type="integer")
     */
    private $paidAmount;

    /**
     * @ORM\OneToMany(targetEntity="DonationLine", mappedBy="donation")
     */
    private $donationLines;
    
    /**
     * @ORM\ManyToOne(targetEntity="Cause", inversedBy="donations")
     * @ORM\JoinColumn(name="cause_id", referencedColumnName="id")
     */
    private $cause;

    
    /**
     * Constructor
     */
    public function __construct() {
        $this->donationLines = new \Doctrine\Common\Collections\ArrayCollection();
    }
    

    public function setTotalAmount($totalAmount) : self {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function getTotalAmount() :? int {
        return $this->totalAmount;
    }

    public function setPaidAmount($paidAmount) : self {
        $this->paidAmount = $paidAmount;
        return $this;
    }
    
    public function getPaidAmount() :? int {
        return $this->paidAmount;
    }
    
    public function addDonationLine(DonationLine $donationLine) : self {
        if ($this->donationLines === null || $this->donationLines->contains($donationLine) === false){
            $this->donationLines->add($donationLine);
        }
        
        return $this;
    }

    public function removeDonationLine(DonationLine $donationLine) : self {
        $this->donationLines->removeElement($donationLine);
    }

    public function getDonationLines() :? Collection {
        return $this->donationLines;
    }

    public function setCause(Cause $cause = null) : self {
        $this->cause = $cause;
        return $this;
    }

    public function getCause() :? Cause {
        return $this->cause;
    }

    public function setAuthor($author) : self {
        $this->author = $author;
        return $this;
    }

    public function getAuthor() :? string {
        return $this->author;
    }

    public function setEmail($email) : self {
        $this->email = $email;
        return $this;
    }

    public function getEmail() :? string {
        return $this->email;
    }

    public function setPhone($phone) : self {
        $this->phone = $phone;
        return $this;
    }

    public function getPhone() :? string {
        return $this->phone;
    }

    public function setAddress($address) : self {
        $this->address = $address;
        return $this;
    }

    public function getAddress() :? string {
        return $this->address;
    }
}
