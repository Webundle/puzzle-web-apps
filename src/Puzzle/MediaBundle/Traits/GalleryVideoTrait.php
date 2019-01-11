<?php
namespace Puzzle\MediaBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * GalleryVideoTrait
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail@com>
 */
trait GalleryVideoTrait
{
    /**
     * @ORM\Column(name="videos", type="array", nullable=true)
     * @var array
     */
    private $videos;
    
    public function setVideos($videos) {
        $this->videos = $videos;
        return $this;
    }
    
    public function addVideo($video) {
        $this->videos = array_unique(array_merge($this->videos, [$video]));
        return $this;
    }
    
    public function removeVideo($video) {
        $this->videos = array_diff($this->videos, [$video]);
        return $this;
    }
    
    public function getVideos() {
        return $this->videos;
    }
}
