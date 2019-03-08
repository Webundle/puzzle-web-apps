<?php

namespace Puzzle\LearningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;

/**
 * Archive
 *
 * @ORM\Table(name="learning_archive")
 * @ORM\Entity(repositoryClass="Puzzle\LearningBundle\Repository\ArchiveRepository")
 */
class Archive
{
    use PrimaryKeyTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="month", type="string", length=255)
     */
    private $month;

    /**
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=255)
     */
    private $year;
    
    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="archive")
     */
    private $posts;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function setMonth($month) : self {
        $this->month = $month;
        return $this;
    }

    public function getMonth() :? int {
        return $this->month;
    }

    public function setYear($year) : self {
        $this->year = $year;
        return $this;
    }
    
    public function getYear() :? int {
        return $this->year;
    }
    
    public function addPost(\Puzzle\LearningBundle\Entity\Post $post) : self {
        $this->posts[] = $post;
        return $this;
    }
    
    public function removePost(\Puzzle\LearningBundle\Entity\Post $post) : self {
        $this->posts->removeElement($post);
        return $this;
    }
    
    public function getPosts(){
        return $this->posts;
    }
}

