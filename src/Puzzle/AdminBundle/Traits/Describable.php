<?php
namespace Puzzle\AdminBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Describable
 * 
 * @author AGNES Gnagne Cedric <cecnho55@gmail.com>
 * 
 */
trait Describable
{
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    private $description;
    
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription() {
        return $this->description;
    }
}
