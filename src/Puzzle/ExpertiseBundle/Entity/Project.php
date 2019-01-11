<?php

namespace Puzzle\ExpertiseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\AdminBundle\Traits\DatetimeTrait;
use Puzzle\UserBundle\Traits\UserTrait;
use Puzzle\MediaBundle\Traits\GalleryPictureable;
use Puzzle\AdminBundle\Traits\DatetimePeriodTrait;
use Puzzle\AdminBundle\Traits\SlugTrait;
use Puzzle\AdminBundle\Traits\Describable;
use Puzzle\AdminBundle\Traits\Nameable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;

/**
 * @author qwincy
 * 
 * Project
 *
 * @ORM\Table(name="expertise_project")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Project
{
    use PrimaryKeyTrait,
        Nameable,
        Describable,
        Timestampable, 
        Blameable, 
        GalleryPictureable, 
        DatetimePeriodTrait,
        Sluggable;

    /**
     * @ORM\Column(name="client", type="string", length=255)
     * @var string
     */
    private $client;

    /**
     * @ORM\Column(name="location", type="string", length=255)
     * @var string
     */
    private $location;
    
    /**
     * @ORM\ManyToOne(targetEntity="Service", inversedBy="projects")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     */
    private $service;
    
    public function getSluggableFields() {
        return ['name'];
    }
    
    public function setClient(string $client) : self {
        $this->client = $client;
        return $this;
    }

    public function getClient() :? string {
        return $this->client;
    }

    public function setLocation(string $location = null) : self {
        $this->location = $location;
        return $this;
    }

    public function getLocation() :? string {
        return $this->location;
    }

    public function setService(Service $service = null) : self {
        $this->service = $service;
        return $this;
    }

    public function getService() :? Service {
        return $this->service;
    }
}
