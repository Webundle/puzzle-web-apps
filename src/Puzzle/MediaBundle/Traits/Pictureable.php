<?php
namespace Puzzle\MediaBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pictureable
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail@com>
 */
trait Pictureable
{
    /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $picture;
    
    public function setPicture($picture) {
        $this->picture = $picture;
        return $this;
    }
    
    public function getPicture() {
        return $this->picture;
    }
}
