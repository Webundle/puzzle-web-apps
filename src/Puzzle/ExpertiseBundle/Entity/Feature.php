<?php

namespace Puzzle\ExpertiseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\AdminBundle\Traits\DatetimeTrait;
use Doctrine\Common\Collections\Collection;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\MediaBundle\Traits\Pictureable;
use Puzzle\UserBundle\Traits\UserTrait;
use Puzzle\AdminBundle\Traits\Nameable;
use Puzzle\AdminBundle\Traits\Describable;
use Puzzle\AdminBundle\Traits\SlugTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;

/**
 * Service
 *
 * @ORM\Table(name="expertise_feature")
 * @ORM\Entity(repositoryClass="Puzzle\ExpertiseBundle\Repository\ServiceRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Feature
{
    use PrimaryKeyTrait, Timestampable, Blameable;
    
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
     * @ORM\Column(name="icon", type="string", length=255, nullable=true)
     * @var string
     */
    private $icon;
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setDescription($description) :self {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription() :?string {
        return $this->description;
    }
    
    public function setIcon($icon) :self {
        $this->icon = $icon;
        return $this;
    }

    public function getIcon() :?string {
        return $this->icon;
    }
}
