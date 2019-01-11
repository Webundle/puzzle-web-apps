<?php
namespace Puzzle\AdminBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * SlugTrait
 * 
 * @author AGNES Gnagne Cedric <cecnho55@gmail.com>
 * 
 */
trait SlugTrait
{
    /**
     * @ORM\Column(name="slug", type="string", nullable=true, unique=true)
     * @var string
     */
    private $slug;
    
    public function setSlug(string $slug) {
        $this->slug = $slug;
        return $this;
    }
    
    public function getSlug(){
        return $this->slug;
    }
}
