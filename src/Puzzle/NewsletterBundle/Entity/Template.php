<?php

namespace Puzzle\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\AdminBundle\Traits\DatetimeTrait;
use Puzzle\MediaBundle\Traits\DocumentTrait;
use Puzzle\AdminBundle\Traits\Describable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;

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
        Blameable
    ;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var string
     * @ORM\Column(name="trigger", type="string", length=255)
     */
    private $trigger;
    
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;
   
    public function setName($name) :self {
        $this->name = $name;
        return $this;
    }

    public function getName() :?string {
        return $this->name;
    }
    
    public function setTrigger($trigger) :self {
        $this->trigger = $trigger;
        return $this;
    }
    
    public function getTrigger() :?string {
        return $this->trigger;
    }
    
    public function setContent($content) :self{
        $this->content = $content;
        return $this;
    }
    
    public function getContent() :?string {
        return $this->content;
    }
}
