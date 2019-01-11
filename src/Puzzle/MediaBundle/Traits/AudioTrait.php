<?php
namespace Puzzle\MediaBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * AudioTrait
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail@com>
 */
trait AudioTrait
{
    /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $audio;
    
    public function setAudio($audio) {
        $this->audio = $audio;
        return $this;
    }
    
    public function getAudio() {
        return $this->audio;
    }
}
