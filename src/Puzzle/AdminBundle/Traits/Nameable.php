<?php
namespace Puzzle\AdminBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Nameable
 * 
 * @author AGNES Gnagne Cedric <cecnho55@gmail.com>
 * 
 */
trait Nameable
{
    /**
	 * @ORM\Column(name="name", type="string", length=255)
	 * @var string
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
