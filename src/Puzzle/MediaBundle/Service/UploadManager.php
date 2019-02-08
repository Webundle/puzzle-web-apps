<?php

namespace Puzzle\MediaBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Puzzle\MediaBundle\Entity\Audio;
use Puzzle\MediaBundle\Entity\Document;
use Puzzle\MediaBundle\Entity\File;
use Puzzle\MediaBundle\Entity\Folder;
use Puzzle\MediaBundle\Entity\Picture;
use Puzzle\MediaBundle\Entity\Video;
use Puzzle\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManager;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class UploadManager
{
	/**
	 * @var EntityManager $em
	 */
	protected $em;
	
	/**
	 * @var FileManager $fm
	 */
	protected $fm;
	
	/**
	 * @var string $name
	 */
	protected $name;
	
	/**
	 * @var string $extension
	 */
	protected $extension;
	
	/**
	 * @var string $filename
	 */
	protected $filename;
	
	/**
	 * @var Folder $folder
	 */
	protected $folder;
	
	/**
	 * @var User $user
	 */
	protected $user;
	
	protected $baseDir;
	
	/**
	 * @param EntityManager $em
	 * @param FileManager $fm
	 * @param mixed $baseDir
	 */
	public function __construct(EntityManager $em, FileManager $fm, $baseDir){
		$this->em = $em;
		$this->fm = $fm;
		$this->baseDir = $baseDir;
	}
	
	/**
	 * Prepare Upload
	 *
	 * @param array $product
	 * @param string $action
	 */
	public function prepareUpload($globalFiles, Folder $folder, User $user)
	{
	    $folder->setBaseDir($this->baseDir);
	    
		$results = [];
		if(count($globalFiles) > 0 ){
			foreach ($globalFiles as $globalFile){
				$originalNames = $globalFile['name'];
				$mimeTypes = $globalFile['type'];
				$path = $globalFile['tmp_name'];
				$errors = $globalFile['error'];
				$size = $globalFile['size'];
			}
			
			if(! is_array($originalNames)){
				$originalNames = [$originalNames];
				$mimeTypes = [$mimeTypes];
				$path = [$path];
				$errors = [$errors];
				$size = [$size];
			}
	
			$length = count($originalNames);
			for ($i = 0; $i < $length; $i++){
				if($originalNames[$i] != null){
					$file = new UploadedFile($path[$i], $originalNames[$i], $mimeTypes[$i], $size[$i]);
					$results[] = $this->upload($file, $folder, $user);
				}
			}
		}
	
		return $results;
	}
	
	/**
	 * Upload file
	 * 
	 * @param UploadedFile $file
	 * @param string $context
	 * @return NULL|\Puzzle\MediaBundle\Entity\Picture|\Puzzle\MediaBundle\Entity\File
	 */
	public function upload(UploadedFile $file, Folder $folder, User $user)
	{
	    $folder->setBaseDir($this->baseDir);
	    
	    $this->user = $user;
	    $this->folder = $folder;
	    $this->name = utf8_encode($file->getClientOriginalName());
		$this->extension = $file->getClientOriginalExtension();
		$this->filename = $folder->getAbsolutePath().'/'.$this->name;
		$count = 0;
		
		// Rename duplicate file
		while(file_exists($this->filename)){
		    $this->filename = $this->folder->getAbsolutePath().'/'.basename($this->filename, '.'.$this->extension);
		    $basename = basename($this->filename, '('.$count.')');
			$count++;
			$this->name = $basename.'('.$count.').'.$this->extension;
			$this->filename = $this->folder->getAbsolutePath().'/'.$this->name;
		}
	
		 // Upload File
		$file = $file->move($this->folder->getAbsolutePath(), $this->name);
		return $this->save();
	}
	
	/**
	 * Upload File from remote url
	 *
	 * @param string $url
	 * @param string $name
	 * @param string $appName
	 * @param string $userId
	 * @return array
	 */
	public function uploadFromUrl(string $url, User $user = null, string $appName = Folder::ROOT_APP_NAME)
	{
	    $url_ary = [];
	    $pattern = '#^(.*://)?([\w\-\.]+)\:?([0-9]*)/(.*)$#';
	    
	    if (preg_match($pattern, $url, $url_ary) && !empty($url_ary[4])) {
	        $maxsize = 10000000; // Limit size
	        $parts = explode("?", substr($url_ary[4],strrpos($url_ary[4],"/")+1));
	        $this->name = $parts[0];
	        $base_get = '/' . $url_ary[4];
	        $port = ( !empty($url_ary[3]) ) ? $url_ary[3] : 80;
	        
	        if ($this->name == ""){
	            return self::ERROR_FILE_NAME;
	        }
	        
	        if (!($fsock = fsockopen($url_ary[2], $port))) {
	            return self::ERROR_NETWORK;
	        }
	        
	        fputs($fsock, "GET $base_get HTTP/1.1\r\n");
	        fputs($fsock, "Host: " . $url_ary[2] . "\r\n");
	        fputs($fsock, "Accept-Language: fr\r\n");
	        fputs($fsock, "Accept-Encoding: UTF-8\r\n");
	        fputs($fsock, "User-Agent: PHP\r\n");
	        fputs($fsock, "Connection: close\r\n\r\n");
	        
	        $data = null;
	        //    			unset($data);
	        while (!feof($fsock)) {
	            $data .= fread($fsock, $maxsize);
	        }
	        
	        fclose($fsock);
	        
	        $matchesContentLength = $matchesContentType  = [];
	        if (!preg_match('#Content-Length\: ([0-9]+)[^ /][\s]+#i', $data, $matchesContentLength) || 
	            !preg_match('#Content-Type\: image/[x\-]*([a-z]+)[\s]+#i', $data, $matchesContentType)
	        ){
	            return 13; //Error downling file...No data
	        }
	        
	        $filesize = $matchesContentLength[1];
	        $filetype = $matchesContentType[1];
	        
	        if ($filesize > 0 && $filesize < $maxsize) {
	            $data = substr($data, strlen($data) - $filesize, $filesize);
	            
	            $folderAbsolutePath = File::getBaseDir().File::getBasePath().'/'.$appName;
	            $filename = $folderAbsolutePath.'/'.$this->name;
	            
	            if (! file_exists($folderAbsolutePath)) {
	                mkdir($folderAbsolutePath, 0777, true);
	            }
	            
	            $count = 0;
	            $file = new File($filename);
	            $this->extension = $file->guessExtension();
	            
	            // Rename duplicate file
	            while(file_exists($this->filename)){
	                $this->filename = $this->folder->getAbsolutePath().'/'.basename($this->filename, '.'.$this->extension);
	                $basename = basename($this->filename, '('.$count.')');
	                $count++;
	                $this->name = $basename.'('.$count.').'.$this->extension;
	                $this->filename = $folderAbsolutePath.'/'.$this->name;
	            }
	            
	            $fptr = fopen($$this->filename, 'wb');
	            $bytes_written = fwrite($fptr, $data, $filesize);
	            fclose($fptr);
	            
	            if ($bytes_written != $filesize){
	                unlink($this->filename);
	                return self::ERROR_WRITING;
	            }
	            
	            $this->user = $user;
	            $this->name = $name;
	            $this->extension = $extension;
	            $this->filename = $filename;
	            
	            $this->folder = $this->fm->createFolder($appName, $user);
	            return $this->save($user);
	        }
	        else {
	            return self::ERROR_FILE_SIZE;
	        }
	    }
	}

	/**
	 * Save file on database
	 * 
	 * @return \Puzzle\MediaBundle\Entity\File
	 */
	public function save() {
	    $file = new File();
	    $file->setName($this->name);
	    $file->setPath($this->folder->getPath().'/'.$this->name);
	    $file->setExtension($this->extension);
	    $file->setSize(filesize($this->filename));
	    
	    $this->em->persist($file);
	    
	    $this->folder->addFile($file->getId());
	    
	    // Categorized file
	    if ($file->isPicture()) {
	        $image = getimagesize($this->filename);
	        
	        $fileType = new Picture($this->filename);
	        $fileType->setWidth($image[0]);
	        $fileType->setHeight($image[1]);
	    }elseif ($file->isAudio()) {
	        $fileType = new Audio();
	    }elseif ($file->isVideo()) {
	        $fileType = new Video();
	    }else {
	        $fileType = new Document();
	    }
	    
	    if ($fileType !== null) {
	        $fileType->setFile($file);
	        $this->em->persist($fileType);
	    }
	    
	    $this->em->flush();
	    return $file;
	}
}
