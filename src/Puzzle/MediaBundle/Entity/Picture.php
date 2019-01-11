<?php

namespace Puzzle\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Picture
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 * @ORM\Table(name="media_picture")
 * @ORM\Entity(repositoryClass="Puzzle\MediaBundle\Repository\PictureRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Picture
{
    /**
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Puzzle\UserBundle\Service\KeygenManager") 
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="width", type="integer")
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(name="height", type="integer")
     */
    private $height;
    
    /**
     * @ORM\OneToOne(targetEntity="File", inversedBy="picture")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     */
    private $file;
    
    public function __construct(string $filename = null) {
        $image = getimagesize($filename);
        $this->width = $image[0];
        $this->height = $image[1];
    }
    
    public function getId() :? int {
        return $this->id;
    }
    
    public function setFile(File $file = null) : self {
        $this->file = $file;
        return $this;
    }
    
    public function getFile() :? File {
        return $this->file;
    }

    public function setWidth($width) : self {
        $this->width = $width;
        return $this;
    }

    public function getWidth() :? int {
        return $this->width;
    }

    public function setHeight($height) : self {
        $this->height = $height;
        return $this;
    }

    public function getHeight() :? int {
        return $this->height;
    }
}
