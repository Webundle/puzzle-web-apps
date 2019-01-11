<?php
namespace Puzzle\AdminBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * LocaleTrait
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 * 
 */
trait LocaleTrait
{
    /**
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     * and it is not necessary because globally locale can be set in listener
     */
    private $locale;
    
    public function setLocale(string $locale = null) {
        $this->locale = $locale;
        return $this;
    }
    
    public function getLocale(){
        return $this->locale;
    }
    
    public function setTranslatableLocale($locale){
        $this->locale = $locale;
    }
}
