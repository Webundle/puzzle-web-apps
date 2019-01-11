<?php
namespace Puzzle\MediaBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentTrait
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail@com>
 */
trait DocumentTrait
{
    /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $document;
    
    public function setDocument($document) {
        $this->document = $document;
        return $this;
    }
    
    public function getDocument() {
        return $this->document;
    }
}
