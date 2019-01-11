<?php
namespace Puzzle\MediaBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * VideoTrait
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail@com>
 */
trait VideoTrait
{
    /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $video;
    
    public function setVideo($video) {
        $this->video = $video;
        return $this;
    }
    
    public function getVideo() {
        return $this->video;
    }
}
