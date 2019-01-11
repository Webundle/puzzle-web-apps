<?php
namespace Puzzle\MediaBundle\Twig;

use Doctrine\ORM\EntityManager;
use Puzzle\MediaBundle\Entity\Folder;
use Puzzle\MediaBundle\Entity\File;
use Puzzle\MediaBundle\Util\MediaUtil;

/**
 *
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class MediaExtension extends \Twig_Extension
{
    /**
     * @var EntityManager $em
     */
    protected $em;
    
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('media_supported_extensions', [$this, 'getMediaSupportedExtensions'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('media_folder_by_slug', [$this, 'getFolderBySlug'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('media_file', [$this, 'getFile'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('media_folders_by_tag', [$this, 'getFoldersByTag'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('media_files_by_folder', [$this, 'getFilesByFolder'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    public function getFolderBySlug($slug) {
        return $this->em->getRepository(Folder::class)->findOneBy(['slug' => $slug]);
    }
    
    public function getFoldersByTag($tag) {
        return $this->em->getRepository(Folder::class)->findBy(['tag' => $tag]);
    }
    
    public function getFilesByFolder($folder, $limit = 10) {
        $folder = $this->em->getRepository(Folder::class)->findOneBy(['slug' => $folder]);
        $files = $list = null;
        $criteria = [];
        
        if ($folder && $folder->getFiles()) {
            foreach ($folder->getFiles() as $key => $file) {
                if ($key == 0) {
                    $list = "'".$file."'";
                }else {
                    $list .= ",'".$file."'";
                }
            }
            
            $criteria[] = ['id', null, 'IN ('.$list.')'];
            $files = $this->em->getRepository(File::class)->customFindBy(null, null, $criteria, ['createdAt' => 'DESC'], $limit);
        }
        
        return $files;
    }
    
    public function getFile($id) {
        return $this->em->getRepository(File::class)->find($id);
    }
    
    public function getMediaSupportedExtensions(string $type = null) {
        $filters = '*';
        
        switch ($type){
            case "picture":
                $filters = "*.(". MediaUtil::supportedPictureExtensions().")";
                break;
            case "audio":
                $filters = "*.(".MediaUtil::supportedAudioExtensions().")";
                break;
            case "video":
                $filters = "*.(".MediaUtil::supportedVideoExtensions().")";
                break;
            case "document":
                $filters = "*.(".MediaUtil::supportedDocumentExtensions().")";
                break;
            default:
                break;
        }
        
        return $filters;
    }
}
