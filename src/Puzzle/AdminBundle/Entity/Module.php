<?php

namespace Puzzle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\AdminBundle\Traits\Nameable;
use Puzzle\AdminBundle\Traits\Describable;

/**
 * Module
 *
 * @ORM\Table(name="admin_module")
 * @ORM\Entity(repositoryClass="Puzzle\AdminBundle\Repository\ModuleRepository")
 */
class Module
{
    use Nameable,
        Describable;
    /**
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Puzzle\UserBundle\Service\KeygenManager") 
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @var string
     */
    private $name;
    
    /**
     * @ORM\Column(name="title", type="string", length=255)
     * @var string
     */
    private $title;
    
    /**
     * @ORM\Column(name="icon", type="string", length=255, nullable=true)
     * @var string
     */
    private $icon;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(name="enable", type="boolean")
     * @var bool
     */
    private $enable;
    
    /**
     * @ORM\Column(name="dependencies", type="array", nullable=true)
     * @var array
     */
    private $dependencies;
    
    /**
     * @ORM\Column(name="parents", type="array", nullable=true)
     * @var array
     */
    private $parents;
   
    public function getId() :? string {
        return $this->id;
    }

    public function setEnable($enable) : self {
        $this->enable = $enable;
        return $this;
    }

    public function getEnable() :? bool {
        return $this->enable;
    }

    public function setTitle($title) : self {
        $this->title = $title;
        return $this;
    }

    public function getTitle() :? string {
        return $this->title;
    }

    public function setIcon($icon) :self {
        $this->icon = $icon;
        return $this;
    }

    public function getIcon() :? string {
        return $this->icon;
    }

    public function setDependencies(array $dependencies = null) : self {
    	$this->dependencies = $dependencies;
    	return $this;
    }
    
    public function addDependency($dependency) : self {
    	$this->dependencies[] = $dependency;
    	$this->dependencies = array_unique($this->dependencies);
    	
    	return $this;
    }
    
    public function removeDependency($dependency) : self {
    	$this->dependencies = array_diff($this->dependencies, [$dependency]);
    	return $this;
    }
    
    public function getDependencies() :? array {
    	return $this->dependencies;
    }
    
    public function setParents($parents) : self {
    	$this->parents = $parents;
    	return $this;
    }
    
    public function addParent($parent) : self {
    	$this->parents[] = $parent;
    	$this->parents = array_unique($this->parents);
    	return $this;
    }
    
    public function removeParent($parent) : self {
    	$this->parents = array_diff($this->parents, [$parent]);
    	return $this;
    }
    
    public function getParents() :? array {
    	return $this->parents;
    }
}
