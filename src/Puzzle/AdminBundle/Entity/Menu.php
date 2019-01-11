<?php

namespace Puzzle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Doctrine\Common\Collections\Collection;
use Puzzle\AdminBundle\Traits\Nameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Menu
 *
 * @ORM\Table(name="admin_menu")
 * @ORM\Entity(repositoryClass="Puzzle\AdminBundle\Repository\MenuRepository")
 */
class Menu
{
    use PrimaryKeyTrait,
        Nameable,
        Sluggable;

    /**
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="menu")
     */
    private $menuItems;
    
    public function __construct() {
        $this->menuItems = new ArrayCollection();
    }
    
    public function getSluggableFields() {
        return ['name'];
    }
    
    public function setMenuItems(Collection $menuItems = null) {
        $this->menuItems = $menuItems;
        return $this;
    }
    
    public function addMenuItem(MenuItem $menuItem) {
        if ($this->menuItems === null ||$this->menuItems->contains($menuItem) === false) {
            $this->menuItems->add($menuItem);
        }
        
        return $this;
    }
    
    public function removeMenuItem(MenuItem $menuItem) {
        $this->menuItems->removeElement($menuItem);
        return $this;
    }
    
    public function getMenuItems() {
        return $this->menuItems;
    }
}
