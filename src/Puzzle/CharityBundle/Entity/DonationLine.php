<?php

namespace Puzzle\CharityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * DonationLine
 *
 * @ORM\Table(name="charity_donation_line")
 * @ORM\Entity(repositoryClass="Puzzle\CharityBundle\Repository\DonationLineRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class DonationLine
{
    use PrimaryKeyTrait, Timestampable;

    /**
     * @var int
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;
    
    /**
     * @var bool
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="donatedAt", type="datetime")
     */
    private $donatedAt;
    
    /**
     * @ORM\ManyToOne(targetEntity="Donation", inversedBy="donationLines")
     * @ORM\JoinColumn(name="donation_id", referencedColumnName="id")
     */
    private $donation;

    
    public function setAmount($amount) :self {
        $this->amount = $amount;
        return $this;
    }

    public function getAmount() :?int {
        return $this->amount;
    }

    public function setStatus($status) :self {
        $this->status = $status;
        return $this;
    }

    public function getStatus() :?bool {
        return $this->status;
    }
    
    public function setDonatedAt($donatedAt) :self {
        $this->donatedAt = $donatedAt;
        return $this;
    }
    
    public function getDonatedAt() :?\DateTime {
        return $this->donatedAt;
    }
    
    public function setDonation(Donation $donation = null) :self {
        $this->donation = $donation;
        return $this;
    }

    public function getDonation() :?Donation {
        return $this->donation;
    }

}
