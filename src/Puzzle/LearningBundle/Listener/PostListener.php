<?php

namespace Puzzle\LearningBundle\Listener;

use Doctrine\ORM\EntityManager;
use Puzzle\LearningBundle\Entity\Archive;
use Puzzle\LearningBundle\Event\PostEvent;

class PostListener
{
    /**
     * @var EntityManager
     */
    private $em;
    
    public function __construct(EntityManager $em){
        $this->em = $em;
    }
    
    public function onCreated(PostEvent $event)
    {
        $post = $event->getPost();
        $now = new \DateTime();
        $archive = $this->em->getRepository(Archive::class)->findOneBy([
            'month' => (int) $now->format("m"),
            'year' => $now->format("Y")
        ]);
        
        if ($archive === null) {
            $archive = new Archive();
            $archive->setMonth((int) $now->format("m"));
            $archive->setYear($now->format("Y"));
            
            $this->em->persist($archive);
        }
        
        $post->setArchive($archive);
    }
}

?>
