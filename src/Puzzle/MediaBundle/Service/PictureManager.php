<?php

namespace Puzzle\MediaBundle\Services;

use Doctrine\ORM\EntityManager;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *        
 */
class PictureManager {
	
	protected  $em;
	protected $box;
	
	public function __construct(EntityManager $em){
		
		$this->em = $em;
	}
	
	/**
	 * Crop picture
	 * 
	 * @param String $source
	 * @param String $width
	 * @param String $height
	 *
	 * @return Object $image_dest
	 */
	public function croping($source, $width, $height, $nameImage)
	{
		$imagine = new Imagine();
		$image_src = $imagine->open($source);
		
		$srcBox = $image_src->getSize();
		$box_fin = new Box($width, $height);
		
		if ($srcBox->getWidth() > $srcBox->getHeight()) {
			$cropPoint = new Point((max($width - $box_fin->getWidth(), 0))/2, 0);
			
		} else {
			$cropPoint = new Point(0, (max($height - $box_fin->getHeight(),0))/2);
		}
		
        $box = new Box($width, $height);
        
        $image_dest = $image_src->thumbnail($box, \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND);
        
        $rootDestination = __DIR__.'/../../../web/uploads/images';
        $image_dest->crop($cropPoint, $box_fin)->save($rootDestination.'/'.$width.'x'.$height.'/'.$nameImage, array('quality' => 100)); 

        return $image_dest;
	}
	
	
	/**
	 * Resize picture width and height
	 * 
	 * @param String $source
	 * @param String $width
	 * @param String $height
	 *
	 * @return Object $image_dest
	 */
	public function resize($source, $width, $height, $nameImage)
	{
		$imagine = new Imagine();
		$size = new Box($width, $height);
		
		$image_src = $imagine->open($source);
		$image_dest = $image_src->resize($size);
		
		$rootDestination = __DIR__.'/../../../web/uploads/images';
		$image_dest->save($rootDestination.'/'.$width.'x'.$height.'/'.$nameImage, array('quality' => 100));
	
		return $image_dest;
	}
	
	/**
	 * Resize picture height
	 *
	 * @param String $source
	 * @param String $width
	 * @param String $height
	 *
	 * @return Object $image_dest
	 */
	public function resizeToHeight($source, $height){
		
		$imagine = new Imagine();
		$image_src = $imagine->open($source);
		
		$ratio = $height / $image_src->getHeight();
		$width = $image_src->getWidth() * $ratio;
		$image_dest = $image_src->resize($width, $height);
		
		return $image_dest;
		
	}
	
	/**
	 * Resize picture width
	 *
	 * @param String $source
	 * @param String $width
	 * @param String $height
	 *
	 * @return Object $image_dest
	 */
	public function resizeToWidth($source, $width){
	
		$imagine = new Imagine();
		$image_src = $imagine->open($source);
		
		$ratio = $width / $image_src->getWidth();
		$height = $image_src->getHeight() * $ratio;
		$image_dest = $image_src->resize($width, $height);
		
		return $image_dest;
	}
}