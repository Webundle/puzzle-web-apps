<?php
namespace Puzzle\MediaBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * GalleryPictureable
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail@com>
 */
trait GalleryPictureable
{
    /**
     * @ORM\Column(name="pictures", type="simple_array", nullable=true)
     * @var array
     */
    private $pictures;
    
    public function setPictures($pictures) {
        $this->pictures = $pictures;
        return $this;
    }
    
    public function addPicture($picture) {
        $this->pictures = array_unique(array_merge($this->pictures, [$picture]));
        return $this;
    }
    
    public function removePicture($picture) {
        $this->pictures = array_diff($this->pictures, [$picture]);
        return $this;
    }
    
    public function getPictures() {
        return $this->pictures;
    }
}
