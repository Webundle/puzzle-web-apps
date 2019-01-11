<?php

namespace Puzzle\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Archive
 *
 * @ORM\Table(name="blog_archive")
 * @ORM\Entity(repositoryClass="Puzzle\BlogBundle\Repository\ArchiveRepository")
 */
class Archive
{
    /**
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Puzzle\UserBundle\Service\KeygenManager") 
     */
    private $id;

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

    public function getId() :? string{
        return $this->id;
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
    
    public function addPost(\Puzzle\BlogBundle\Entity\Post $post) : self {
        $this->posts[] = $post;
        return $this;
    }
    
    public function removePost(\Puzzle\BlogBundle\Entity\Post $post) : self {
        $this->posts->removeElement($post);
        return $this;
    }
    
    public function getPosts(){
        return $this->posts;
    }
}

