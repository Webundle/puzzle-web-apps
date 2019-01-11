<?php

namespace Puzzle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Doctrine\Common\Collections\Collection;
use Puzzle\StaticBundle\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MenuItem
 *
 * @ORM\Table(name="admin_menu_item")
 * @ORM\Entity(repositoryClass="Puzzle\AdminBundle\Repository\MenuItemRepository")
 */
class MenuItem
{
    use PrimaryKeyTrait;
    
    /**
     * @ORM\Column(name="name", type="string", length=255)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="position", type="integer")
     * @var int
     */
    private $position;
    
    /**
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="menuItems")
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id")
     */
    private $menu;
    
    /**
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="parent")
     */
    private $childs;
    
    /**
     * @ORM\ManyToOne(targetEntity="MenuItem", inversedBy="childs")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;
    
    /**
     * @ORM\ManyToOne(targetEntity="Puzzle\StaticBundle\Entity\Page")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     */
    private $page;
    
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setPosition($position) {
        $this->position = $position;
        return $this;
    }

    public function getPosition() {
        return $this->position;
    }

    public function setMenu(Menu $menu) {
        $this->menu = $menu;
        return $this;
    }

    public function getMenu() {
        return $this->menu;
    }
    
    public function setParent(MenuItem $parent = null){
        $this->parent = $parent;
        return $this;
    }
    
    public function getParent(){
        return $this->parent;
    }
    
    public function addChild(MenuItem $child) : self {
        if ($this->childs === false or $this->childs->contains($child) === false){
            $this->childs->add($child);
        }
        return $this;
    }
    
    public function removeChild(MenuItem $child) : self{
        $this->childs->removeElement($child);
        return $this;
    }
    
    public function getChilds() :? Collection{
        return $this->childs;
    }
    
    public function setPage(Page $page = null) {
        $this->page = $page;
        return $this;
    }
    
    public function getPage() {
        return $this->page;
    }
}
