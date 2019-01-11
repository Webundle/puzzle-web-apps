<?php

namespace Puzzle\ExpertiseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\MediaBundle\Traits\Pictureable;
use Puzzle\AdminBundle\Traits\DatetimeTrait;
use Puzzle\UserBundle\Traits\UserTrait;
use Puzzle\AdminBundle\Traits\Nameable;

/**
 * Partner
 *
 * @ORM\Table(name="expertise_partner")
 * @ORM\Entity(repositoryClass="Puzzle\ExpertiseBundle\Repository\PartnerRepository")
 */
class Partner
{
    use PrimaryKeyTrait,
        Nameable,
        Pictureable,
        DatetimeTrait,
        UserTrait;
    
    /**
     * @var string
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    private $location;
    
    /**
     * @var string
     * @ORM\Column(name="tags", type="array", nullable=true)
     */
    private $tags;

    public function setLocation($location) : self {
        $this->location = $location;
        return $this;
    }
    
    public function getLocation() :? string {
        return $this->location;
    }
    
    public function setTags($tags = null) : self {
        $this->tags = $tags;
        return $this;
    }
    
    public function addTag($tag) : self {
        $this->tags = array_unique(array_merge($this->tags, [$tag]));
        return $this;
    }
    
    public function removeTag($tag) : self {
        $this->tags = array_diff($this->tags, [$tag]);
        return $this;
    }
    
    public function getTags() :? array {
        return $this->tags;
    }

}
