<?php

namespace Puzzle\CharityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Doctrine\Common\Collections\Collection;

/**
 * Charity Donation
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
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
     * @var int
     * @ORM\Column(name="count_donation_lines", type="integer")
     */
    private $countDonationLines;
    
    /**
     * @var string
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
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
     * @ORM\ManyToOne(targetEntity="Member", inversedBy="donations")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     */
    private $member;

    
    /**
     * Constructor
     */
    public function __construct() {
        $this->donationLines = new \Doctrine\Common\Collections\ArrayCollection();
        $this->countDonationLines = 0;
        $this->paidAmount = 0;
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
    
    public function setCountDonationLines($countDonationLines) : self {
        $this->countDonationLines = $countDonationLines;
        return $this;
    }
    
    public function getCountDonationLines() :? int {
        return $this->countDonationLines;
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
    
    public function setMember(Member $member) :self {
        $this->member = $member;
        return $this;
    }
    
    public function getMember() :?Member {
        return $this->member;
    }

    public function getAmountRatio() {
        $ratio = null;
        
        if ($this->totalAmount > 0) {
            $ratio = $this->paidAmount / $this->totalAmount;
            $ratio = round($ratio * 100, 2).' %';
        }
        
        return $ratio;
    }
}
