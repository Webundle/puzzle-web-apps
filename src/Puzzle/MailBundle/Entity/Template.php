<?php

namespace Puzzle\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\AdminBundle\Traits\DatetimeTrait;
use Puzzle\MediaBundle\Traits\DocumentTrait;
use Puzzle\AdminBundle\Traits\Describable;

/**
 * Template
 *
 * @ORM\Table(name="mail_template")
 * @ORM\Entity(repositoryClass="Puzzle\MailBundle\Repository\TemplateRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Template
{
    use PrimaryKeyTrait,
        DatetimeTrait,
        DocumentTrait,
        Describable;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
   
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getName() {
        return $this->name;
    }
}
