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
 * Charity Cause
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 * @ORM\Table(name="charity_cause")
 * @ORM\Entity(repositoryClass="Puzzle\CharityBundle\Repository\CauseRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Cause
{
    use PrimaryKeyTrait,
        Timestampable
    ;
    
    /**
     * @ORM\Column(name="name", type="string", length=255)
     * @var string
     */
    private $name;
    
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    private $description;
    
    /**
     * @ORM\Column(name="expiresAt", type="datetime", nullable=true)
     * @var \DateTime
     */
    private $expiresAt;
    
    /**
     * @ORM\Column(name="visible", type="boolean")
     * @var boolean
     */
    private $visible;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $picture;
    
    /**
    * @ORM\Column(name="tag", type="simple_array", nullable=true)
    * @var array
    */
    private $tags;
    
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
    
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function setPicture($picture) {
        $this->picture = $picture;
        return $this;
    }
    
    public function getPicture() {
        return $this->picture;
    }
    
    public function setTags($tags) : self {
        $this->tags = $tags;
        return $this;
    }
    
    public function addTag($tag) : self {
        $this->tags[] = $tag;
        $this->tags = array_unique($this->tags);
        
        return $this;
    }
    
    public function removeTag($tag) : self {
        $this->tags = array_diff($this->tags, [$tag]);
        return $this;
    }
    
    public function getTags() :? array {
        return $this->tags;
    }
    
    public function setVisible(bool $visible) : self {
        $this->visible = $visible;
        return $this;
    }
    
    public function isVisible() :? bool {
        return $this->visible;
    }
    
    public function setExpiresAt(\DateTime $expiresAt) :self {
        $this->expiresAt = $expiresAt;
        return $this;
    }
    
    public function getExpiresAt() :?\DateTime {
        return $this->expiresAt;
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
        $ratio = null;
        
        if ($this->totalAmount > 0) {
            $ratio = $this->paidAmount / $this->totalAmount;
            $ratio = round($ratio * 100, 2).' %';
        }
        
        return $ratio;
    }
    
    public function isClosable() {
        return $this->expiresAt !== null;
    }
    
    public function isClosed() {
        return $this->expiresAt->getTimestamp() < time();
    }
}
