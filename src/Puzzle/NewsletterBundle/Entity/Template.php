<?php

namespace Puzzle\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\AdminBundle\Traits\DatetimeTrait;
use Puzzle\MediaBundle\Traits\DocumentTrait;
use Puzzle\AdminBundle\Traits\Describable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;

/**
 * Template
 *
 * @ORM\Table(name="newsletter_template")
 * @ORM\Entity(repositoryClass="Puzzle\NewsletterBundle\Repository\TemplateRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Template
{
    use PrimaryKeyTrait,
        Timestampable,
        Sluggable,
        Blameable
    ;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var string
     * @ORM\Column(name="event", type="string", length=255, nullable=true)
     */
    private $event;
    
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;
   
    public function getSluggableFields() {
        return ['name'];
    }
    
    public function setName($name) :self {
        $this->name = $name;
        return $this;
    }

    public function getName() :?string {
        return $this->name;
    }
    
    public function setEvent($event) :self {
        $this->event = $event;
        return $this;
    }
    
    public function getEvent() :?string {
        return $this->event;
    }
    
    public function setContent($content) :self{
        $this->content = $content;
        return $this;
    }
    
    public function getContent() :?string {
        return $this->content;
    }
}
