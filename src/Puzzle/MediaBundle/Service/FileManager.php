<?php

namespace Puzzle\MediaBundle\Service;

use Puzzle\MediaBundle\Entity\File;
use Puzzle\MediaBundle\Entity\Folder;
use Puzzle\MediaBundle\Entity\Picture;
use Puzzle\MediaBundle\Entity\Audio;
use Puzzle\MediaBundle\Entity\Video;
use Puzzle\MediaBundle\Entity\Document;
use Doctrine\ORM\EntityManager;
use Puzzle\UserBundle\Entity\User;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *        
 */
class FileManager {
    
    /**
     * @param EntityManager $em
     */
    protected $em;
    
    public function __construct(EntityManager $em, $baseDir) {
        $this->em = $em;
        $this->baseDir = $baseDir;
    }
    
    /**
     * Create folder
     * 
     * @param string $folderTarget
     * @param User $user
     * @param bool $apply
     * @return \Puzzle\MediaBundle\Entity\Folder|object|NULL
     */
    public function createFolder(string $folderTarget, User $user, bool $apply = false) {
        if ($folderTarget !== null) {
            $folder = $this->em->getRepository(Folder::class)->find($folderTarget);
            
            if ($folder === null) {
                $folderNames = explode('/', $folderTarget);
                $appName = $folderNames[0];
                $array = [];
                
                foreach ($folderNames as $key => $folderName){
                    $folder = $this->em->getRepository(Folder::class)->findOneBy(array(
                        "appName" => $appName,
                        "name" => $folderName
                    ));
                    
                    if ($folder === null){
                        $folder = new Folder();
                        $folder->setName($folderName);
                        $folder->setAppName($appName);
                        
                        if ($key > 0){
                            $folder->setParent($array[$key - 1]);
                        }
                        
                        $this->em->persist($folder);
                    }
                    
                    $array[$key] = $folder;
                }
            }
            
            $folder->setBaseDir($this->baseDir);
        }else {
            $folder = $this->em->getRepository(Folder::class)->findOneBy([
                'appName' => Folder::ROOT_APP_NAME,
                'name' => Folder::ROOT_NAME
            ]);
            
            if ($folder === null) {
                $folder = new Folder();
                $folder->createDefault();
                
                $this->em->persist($folder);
                $$this->em->flush($folder);
            }
            
            $folder->setBaseDir($this->baseDir);
        }
        
        if ($apply === true && file_exists($folder->getAbsolutePath()) === false) {
            mkdir($folder->getAbsolutePath(), 0777, true);
        }
        
        return $folder;
    }
    
    /**
     * Remove folder and its content
     * 
     * @param string $folderPath
     * @return boolean
     */
    public function removeDirectory($folderPath) {
        $files = scandir($folderPath, SCANDIR_SORT_DESCENDING);
        foreach ($files as $file) {
            if ($file != "." && $file != ".."){
                $file = $folderPath.'/'.$file;
                is_dir($file) ? self::removeDirectory($file) : unlink($file);
            }
        }
        
        return rmdir($folderPath);
    }
    
    
    /**
     * Rename file or directory 
     * 
     * @param string $name
     * @param string $folderPath
     * @param string $extension
     * @param bool $applyOnDisk
     * @return string
     */
    public function rename(string $name, string $folderPath, string $extension = null, bool $applyOnDisk = false) {
        $oldFilename = $filename = $folderPath.'/'.$name;
        $count = 0;
        
        // Rename duplicate file
        while(file_exists($filename)){
            if ($extension !== null){
                $filename = $folderPath.'/'.basename($filename, '.'.$extension);
            }else {
                $filename = $folderPath.'/'.basename($filename);
            }
            
            $basename = basename($filename, '('.$count.')');
            $count++;
            
            if ($extension !== null){
                $name = $basename.'('.$count.').'.$extension;
            }else {
                $name = $basename.'('.$count.')';
            }
            
            $filename = $folderPath.'/'.$name;
        }
        
        if ($applyOnDisk == true) {
            rename($oldFilename, $filename);
        }
        
        return $filename;
    }
    
    /**
     * Copy file
     * 
     * @param File $file
     * @param Folder $folder
     * @return \Puzzle\MediaBundle\Entity\File
     */
    public function copyFile(File $file, Folder $folder, bool $isOverwritable = false)
    {
        $folder->setBaseDir($this->baseDir);
        $name= $file->getName();
        $extension = $file->getExtension();
        
        if ($isOverwritable === false){ // Rename file
            $filename = self::rename($name, $folder->getAbsolutePath(), $extension);
        }else { // Overwrite
            $filename = $folder->getPath().'/'.$name;
        }
        
        $name = basename($filename);
        //Copy file on local storage
        copy($file->getAbsolutePath(), $folder->getAbsolutePath().'/'.$name);
        
        // Save file on database
        $newFile = new File();
        $newFile->setName(utf8_encode($name));
        $newFile->setPath($folder->getPath().'/'.utf8_encode($name));
        $newFile->setExtension($extension);
        $newFile->setSize($file->getSize());
        
        $this->em->persist($newFile);
        $folder->addFile($newFile->getId());
        
        // Categorized file
        if ($newFile->isPicture()) {
            $fileType = new Picture($filename);
        }elseif ($newFile->isAudio()) {
            $fileType = new Audio();
        }elseif ($newFile->isVideo()) {
            $fileType = new Video();
        }elseif ($newFile->isDocument()) {
            $fileType = new Document();
        }
        
        if ($fileType !== null) {
            $fileType->setFile($newFile);
            $this->em->persist($fileType);
        }
        
        $this->em->flush();
        return $newFile;
    }
    
    /**
     * Move file
     * 
     * @param File $file
     * @param Folder $folder
     */
    public function moveFile(File $file, Folder $folder) {
        // Rename file if already exist
        $filename = self::rename($file->getName(), $folder->getAbsolutePath(), $file->getExtension());
        $name = basename($filename);
        //Copy file on local storage
        copy($file->getAbsolutePath(), $folder->getAbsolutePath().'/'.$name);
        // Delete old file
        unlink($file->getAbsolutePath());
        
        // Update file infos in database
        $file->setName($name);
        $file->setPath($folder->getPath().'/'.$name);
        
        $this->em->flush();
        return $file;
    }
    
    
    /**
     * Add files and sub-directories in a folder to zip file.
     * @param string $folder
     * @param \ZipArchive $zipFile
     * @param int $exclusiveLength Number of text to be exclusived from the file path.
     */
    private static function folderToZip($folder, &$zipFile, $exclusiveLength) {
        $handle = opendir($folder);
        while (false !== $f = readdir($handle)) {
            if ($f != '.' && $f != '..') {
                $filePath = "$folder/$f";
                // Remove prefix from file path before add to zip.
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    // Add sub-directory.
                    $zipFile->addEmptyDir($localPath);
                    self::folderToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }
        closedir($handle);
    } 
    
    /**
     * Zip a folder (include itself).
     * Usage:
     *   HZip::zipDir('/path/to/sourceDir', '/path/to/out.zip');
     *
     * @param string $sourcePath Path of directory to be zip.
     * @param string $outZipPath Path of output zip file.
     */
    public function zipDir($source)
    {
        $outZipPath = $source . '.zip';
        $pathInfo = pathInfo($source);
        $dirName = $pathInfo['basename'];
        
        $webDir = explode('../web/', $source);
        $parentDir = getcwd().DIRECTORY_SEPARATOR.($webDir[1] ?? 'uploads'.DIRECTORY_SEPARATOR.$dirName);
        $outZipPath = $parentDir.'.zip';
        
        // create new archive
        $zipFile = new \PhpZip\ZipFile();
        try{
            $zipFile
                ->addDirRecursive($source, $parentDir) // add files from the directory
                ->saveAsFile($outZipPath) // save the archive to a file
                ->close(); // close archive
        }
        catch(\PhpZip\Exception\ZipException $e){
            // handle exception
        }
        finally{
            $zipFile->close();
        }
        
        return $outZipPath;
    }
}