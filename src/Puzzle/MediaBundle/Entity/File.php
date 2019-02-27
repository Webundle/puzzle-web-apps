<?php

namespace Puzzle\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\MediaBundle\Util\MediaUtil;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Doctrine\Common\Collections\Collection;

/**
 * File
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 * @ORM\Table(name="media_file")
 * @ORM\Entity(repositoryClass="Puzzle\MediaBundle\Repository\FileRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class File
{
    use PrimaryKeyTrait,
        Timestampable,
        Blameable
    ;
    
    /**
     * @ORM\Column(name="name", type="string", length=255)
     * @var string
     */
    private $name;
    
    /**
     * @var string
     * @ORM\Column(name="caption", type="string", length=255, nullable=true)
     */
    private $caption;
    
    /**
     * @ORM\Column(name="path", type="string", length=255)
     * @var string
     */
    private $path;
    
    /**
     * @ORM\Column(name="extension", type="string", length=255, nullable=true)
     * @var string
     */
    private $extension;

    /**
     * @ORM\Column(name="size", type="integer", nullable=true)
     * @var int
     */
    private $size;
    
    /**
     * @ORM\Column(name="enable_comments", type="boolean")
     * @var boolean
     */
    private $enableComments;
    
    /**
     * @ORM\OneToOne(targetEntity="Picture", mappedBy="file", cascade={"persist", "remove"})
     */
    private $picture;
    
    /**
     * @ORM\OneToOne(targetEntity="Audio", mappedBy="file", cascade={"persist", "remove"})
     */
    private $audio;
    
    /**
     * @ORM\OneToOne(targetEntity="Video", mappedBy="file", cascade={"persist", "remove"})
     */
    private $video;
    
    /**
     * @ORM\OneToOne(targetEntity="Document", mappedBy="file", cascade={"persist", "remove"})
     */
    private $document;
    
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="file")
     */
    private $comments;
    
    protected $baseDir;
    
    public function __construct(array $properties = null) {
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->enableComments = true;
        
        if (isset($properties['name']) === true) {
            $this->name = $properties['name'];
        }
        
        if (isset($properties['context']) === true) {
            $this->context = $properties['context'];
        }
        
        if (isset($properties['path']) === true) {
            $this->path = $properties['path'];
        }
    }

    public function setName($name) : self {
        $this->name = utf8_encode($name);
        return $this;
    }

    public function getName() :? string {
        return utf8_decode($this->name);
    }
    
    public function getOriginalName() :? string {
        return $this->name;
    }
    
    public function setCaption($caption){
        $this->caption = $caption;
        return $this;
    }
    
    public function getCaption(){
        return $this->caption;
    }

    public function setExtension($extension) : self {
        $this->extension = $extension;
        return $this;
    }

    public function getExtension() :? string {
        return $this->extension;
    }

    public function setSize($size) : self {
        $this->size = $size;
        return $this;
    }

    public function getSize() :? int {
        return $this->size;
    }
    
    public function setPath($path) : self {
    	$this->path = $path;
    	return $this;
    }
    
    public function getPath() :? string {
    	return $this->path;
    }
    
    public function setEnableComments($enableComments) : self {
        $this->enableComments = $enableComments;
        return $this;
    }
    
    public function getEnableComments() :? bool {
        return $this->enableComments;
    }
    
    public function setBaseDir($baseDir) {
        $this->baseDir = $baseDir;
        return $this;
    }
    
    public function getBaseDir(){
        return $this->baseDir;
    }
    
    public static function getBasePath(){
    	return '/uploads';
    }
    
    public function getAbsolutePath(){
    	return $this->getBaseDir().$this->path;
    }

    public function setPicture(Picture $picture = null) : self {
        $this->picture = $picture;
        return $this;
    }

    public function getPicture() :? Picture {
        return $this->picture;
    }

    public function setAudio(Audio $audio = null) : self {
        $this->audio = $audio;
        return $this;
    }

    public function getAudio() :? Audio {
        return $this->audio;
    }

    public function setVideo(Video $video = null) : self {
        $this->video = $video;
        return $this;
    }

    public function getVideo() :? Video {
        return $this->video;
    }

    public function setDocument(Document $document = null) : self {
        $this->document = $document;
        return $this;
    }

    public function getDocument() :? Document {
        return $this->document;
    }
    
    public function addComment(Comment $comment) : self {
        if ($this->comments === null || $this->comments->contains($comment) === false) {
            $this->comments->add($comment);
        }
        
        return $this;
    }
    
    public function removeComment(Comment $comment) : self {
        $this->comments->removeElement($comment);
    }
    
    public function getComments() :? Collection {
        return $this->comments;
    }
    
    public function isPicture() {
        return true === in_array($this->extension, explode('|', MediaUtil::supportedPictureExtensions()));
    }
    
    public function isAudio() {
        return true === in_array($this->extension, explode('|', MediaUtil::supportedAudioExtensions()));
    }
    
    public function isVideo() {
        return true === in_array($this->extension, explode('|', MediaUtil::supportedVideoExtensions()));
    }
    
    public function isDocument() {
        return true === in_array($this->extension, explode('|', MediaUtil::supportedDocumentExtensions()));
    }
    
    /**
     * Converts bytes into human readable file size.
     *
     * @param string $bytes
     * @return string human readable file size (2,87 Мб)
     * @author Mogilev Arseny
     */
    public function sizeConvert()
    {
        $result= null;
        $bytes = floatval($this->size);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );
        
        foreach($arBytes as $arItem)
        {
            if($bytes >= $arItem["VALUE"])
            {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
                break;
            }
        }
        
        return $result;
    }
}
