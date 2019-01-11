<?php

namespace Puzzle\CharityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\AdminBundle\Traits\Nameable;
use Puzzle\AdminBundle\Traits\Describable;
use Puzzle\MediaBundle\Traits\Pictureable;
use Puzzle\AdminBundle\Traits\DatetimePeriodTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Puzzle\AdminBundle\Traits\Taggable;
use Doctrine\Common\Collections\Collection;

/**
 * Cause
 *
 * @ORM\Table(name="charity_cause")
 * @ORM\Entity(repositoryClass="Puzzle\CharityBundle\Repository\CauseRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Cause
{
    use PrimaryKeyTrait,
        Nameable,
        Describable,
        Pictureable,
        DatetimePeriodTrait,
        Timestampable,
        Taggable;
    
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
     * @ORM\OneToMany(targetEntity="Donation", mappedBy="cause")
     */
    private $donations;
    
    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="causes")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * Constructor
     */
    public function __construct() {
    	$this->donations = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->paidAmount = 0;
    	$this->totalAmount = 0;
    }
    
    public function getSlugglableFields() {
        return ['name'];
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

    public function addDonation(Donation $donation) : self {
        if ($this->donations === null || $this->donations->contains($donation) === false){
            $this->donations->add($donation);
        }
        
        return $this;
    }

    public function removeDonation(Donation $donation) : self {
        $this->donations->removeElement($donation);
    }

    public function getDonations() :? Collection {
        return $this->donations;
    }

    public function setCategory(Category $category = null) : self {
        $this->category = $category;
        return $this;
    }

    public function getCategory() :? Category {
        return $this->category;
    }
    
    public function getAmountRatio() {
        return $this->totalAmount > 0 ? $this->paidAmount / $this->totalAmount : null;
    }
    
    public function isClosable() {
        return $this->endedAt !== null;
    }
    
    public function isClosed() {
        return $this->endedAt->getTimestamp() < time();
    }
}
