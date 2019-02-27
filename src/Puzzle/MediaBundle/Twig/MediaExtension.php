<?php
namespace Puzzle\MediaBundle\Twig;

use Doctrine\ORM\EntityManager;
use Puzzle\MediaBundle\Entity\Folder;
use Puzzle\MediaBundle\Entity\File;
use Puzzle\MediaBundle\Util\MediaUtil;
use Knp\Component\Pager\Paginator;

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
    
    /**
     * @var Paginator $paginator
     */
    protected $paginator;
    
    public function __construct(EntityManager $em, Paginator $paginator) {
        $this->em = $em;
        $this->paginator = $paginator;
    }
    
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('puzzle_media_supported_extensions', [$this, 'getMediaSupportedExtensions'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_media_folders', [$this, 'getFolders'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_media_folder', [$this, 'getFolder'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_media_folder_files', [$this, 'getFolderFiles'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_media_files', [$this, 'getFiles'], ['needs_environment' => false, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('puzzle_media_file', [$this, 'getFile'], ['needs_environment' => false, 'is_safe' => ['html']]),
        ];
    }
    
    public function getFolders(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(Folder::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(Folder::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getFolder($id) {
        if (!$folder = $this->em->find(Folder::class, $id)) {
            $folder =  $this->em->getRepository(Folder::class)->findOneBy(['slug' => $id]);
        }
        
        return $folder;
    }
    
    public function getFolderFiles($folderId, $page = 1, $limit = null) {
        $folder = $this->getFolder($folderId);
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
            
            $criteria[] = ['key' => 'id', 'value' => null, 'operator' => 'IN ('.$list.')'];
            
            if (is_int($limit) === true) {
                $query = $this->em->getRepository(Folder::class)->customGetQuery(null, null, $criteria, ['createdAt' => 'DESC'], $limit);
                return $this->paginator->paginate($query, $page, $limit);
            }
            
            return  $this->em->getRepository(File::class)->customFindBy(null, null, $criteria, ['createdAt' => 'DESC'], null);
        }
        
        return $files;
    }
    
    public function getFiles(array $fields = [], array $joins =[], array $criteria = [], array $orderBy = ['createdAt' => 'DESC'], $limit = null, int $page = 1) {
        if (is_int($limit) === true) {
            $query = $this->em->getRepository(File::class)->customGetQuery($fields, $joins, $criteria, $orderBy, null, null);
            return $this->paginator->paginate($query, $page, $limit);
        }
        
        return  $this->em->getRepository(File::class)->customFindBy($fields, $joins, $criteria, $orderBy, null, null);
    }
    
    public function getFile($id) {
        if (! $file = $this->em->find(File::class, $id)) {
            $file = $this->em->getRepository(File::class)->findOneBy(['path' => $id]);
        }
        
        return $file;
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
