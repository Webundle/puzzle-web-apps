<?php

namespace Puzzle\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Audio 
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 * @ORM\Table(name="media_audio")
 * @ORM\Entity(repositoryClass="Puzzle\MediaBundle\Repository\AudioRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Audio
{
    /**
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Puzzle\UserBundle\Service\KeygenManager") 
     */
    private $id;
    
    /**
     * @ORM\OneToOne(targetEntity="File", inversedBy="audio")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     */
    private $file;

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
}
