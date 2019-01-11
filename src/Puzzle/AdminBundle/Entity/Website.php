<?php

namespace Puzzle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\AdminBundle\Traits\Nameable;
use Puzzle\AdminBundle\Traits\Describable;
use Puzzle\MediaBundle\Traits\Pictureable;

/**
 * Website
 *
 * @ORM\Table(name="admin_website")
 * @ORM\Entity(repositoryClass="Puzzle\AdminBundle\Repository\WebsiteRepository")
 */
class Website
{
    use PrimaryKeyTrait,
        Nameable,
        Describable;

    /**
     * @ORM\Column(name="time_format", type="string", length=255)
     * @var string
     */
    private $timeFormat;

    /**
     * @ORM\Column(name="date_format", type="string", length=255)
     * @var string
     */
    private $dateFormat;

    /**
     * @ORM\Column(name="email", type="string", length=255)
     * @var string
     */
    private $email;

    public function setTimeFormat($timeFormat) : self {
        $this->timeFormat = $timeFormat;
        return $this;
    }

    public function getTimeFormat() :? string {
        return $this->timeFormat;
    }

    public function setDateFormat($dateFormat) : self {
        $this->dateFormat = $dateFormat;
        return $this;
    }

    public function getDateFormat() :? string {
        return $this->dateFormat;
    }

    public function setEmail($email) : self {
        $this->email = $email;
        return $this;
    }

    public function getEmail() :? string {
        return $this->email;
    }
}

