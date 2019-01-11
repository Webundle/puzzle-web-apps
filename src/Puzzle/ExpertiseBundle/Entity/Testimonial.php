<?php

namespace Puzzle\ExpertiseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\MediaBundle\Traits\Pictureable;
use Puzzle\AdminBundle\Traits\DatetimeTrait;
use Puzzle\AdminBundle\Traits\Describable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;

/**
 * Testimonial
 *
 * @ORM\Table(name="expertise_testimonial")
 * @ORM\Entity(repositoryClass="Puzzle\ExpertiseBundle\Repository\TestimonialRepository")
 */
class Testimonial
{
    use PrimaryKeyTrait,
        Blameable,
        Pictureable,
        DatetimeTrait,
        Describable;
    
    /**
     * @var string
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @var string
     * @ORM\Column(name="company", type="string", length=255)
     */
    private $company;
    
    /**
     * @var string
     * @ORM\Column(name="position", type="string", length=255)
     */
    private $position;

    public function setAuthor($author) : self {
    	$this->author = $author;
        return $this;
    }

    public function getAuthor() :? string {
    	return $this->author;
    }

    public function setCompany($company) : self {
        $this->company = $company;
        return $this;
    }

    public function getCompany() :? string {
        return $this->company;
    }

    public function setPosition($position) : self {
        $this->position = $position;
        return $this;
    }

    public function getPosition() :? string {
        return $this->position;
    }
}
