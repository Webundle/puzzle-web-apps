<?php

namespace Puzzle\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Entity\User;
use Doctrine\Common\Collections\Collection;
use Puzzle\AdminBundle\Traits\Describable;
use Puzzle\AdminBundle\Traits\Nameable;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Folder
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 * @ORM\Table(name="media_folder")
 * @ORM\Entity(repositoryClass="Puzzle\MediaBundle\Repository\FolderRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Folder
{
    use PrimaryKeyTrait,
        Timestampable,
        Describable,
        Nameable,
        Sluggable,
        Blameable;
    
    const ROOT_NAME = "media";
    const ROOT_APP_NAME = "media";
    const ROOT_CONTEXT = "media";
    
    /**
     * @ORM\Column(name="app_name", type="string", length=255)
     * @var string
     */
    private $appName;
    
    /**
     * @ORM\Column(name="path", type="string", length=255)
     * @var string
     */
    private $path;
    
    /**
     * @ORM\Column(name="tag", type="string", length=255, nullable=true)
     * @var string
     */
    private $tag;
    
    /**
     * @ORM\Column(name="allowed_extensions", type="simple_array", nullable=true)
     * @var array
     */
    private $allowedExtensions;

    /**
     * @ORM\Column(name="files", type="simple_array", nullable=true)
     * @var array
     */
    private $files;
    
    /**
     * @ORM\OneToMany(targetEntity="Folder", mappedBy="parent", cascade={"remove"})
     */
    private $childs;
    
    /**
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="childs")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;
    
    protected $baseDir;
    
    public function __construct() {
        $this->childs = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getSluggableFields() {
        return  ['name'];
    }

    public function setFiles($files) : self {
    	$this->files = $files;
    	return $this;
    }
    
    public function addFile($file) : self {
    	$this->files[] = $file;
    	$this->files = array_unique($this->files);
    	
    	return $this;
    }
    
    public function removeFile($file) : self {
    	$this->files = array_diff($this->files, [$file]);
    	return $this;
    }
    
    public function getFiles() :? array {
    	return $this->files;
    }

    public function setAppName($appName) : self {
        $this->appName = $appName;
        return $this;
    }

    public function getAppName() :? string {
        return $this->appName;
    }
    
    public function setTag($tag){
        $this->tag = $tag;
        return $this;
    }
    
    public function getTag(){
        return $this->tag;
    }
    
    public function setParent(Folder $parent = null) : self {
        $this->parent = $parent;
        return $this;
    }
    
    public function getParent() :? self {
        return $this->parent;
    }

    public function addChild(Folder $child) : self {
        if ($this->childs === null || $this->childs->contains($child) === false ) {
            $this->childs->add($child);
        }
        
        return $this;
    }

    public function removeChild(Folder $child) : self {
        $this->childs->removeElement($child);
    }

    public function getChilds() :? Collection {
        return $this->childs;
    }
    
    public function setAllowedExtensions($allowedExtensions) : self {
        $this->allowedExtensions = $allowedExtensions;
        return $this;
    }

    public function getAllowedExtensions() {
        return $this->allowedExtensions;
    }

    /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function setPath() {
        $this->path = $this->parent === null ? File::getBasePath().'/'.$this->name : $this->parent->getPath().'/'.$this->name;
    }

    public function getPath() :? string {
        return $this->path;
    }
    
    public function setBaseDir($baseDir) {
        $this->baseDir = $baseDir;
        return $this;
    }
    
    public function getBaseDir(){
        return $this->baseDir;
    }
    
    public function getAbsolutePath() {
        return $this->getBaseDir().$this->path;
    }
    
    public function createDefault() {
        $this->appName = self::ROOT_APP_NAME;
        $this->name = self::ROOT_NAME;
    }
}
