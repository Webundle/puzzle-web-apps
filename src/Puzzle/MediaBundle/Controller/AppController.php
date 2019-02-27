<?php

namespace Puzzle\MediaBundle\Controller;

use Puzzle\MediaBundle\Entity\Folder;
use Puzzle\MediaBundle\Entity\File;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\MediaBundle\Entity\CommentVote;
use Symfony\Component\HttpFoundation\JsonResponse;
use Puzzle\MediaBundle\Entity\Comment;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class AppController extends Controller
{
    
    /**
     * List files
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listFilesAction(Request $request) {
        return $this->render("AppBundle:Media:list_files.html.twig", array(
            'files' => $this->getDoctrine()->getRepository(File::class)->findBy([], ['createdAt' => 'DESC'])
        ));
    }
    
    /**
     * Show File
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFileAction(Request $request, $id) {
        $em = $this->get('doctrine.orm.entity_manager');
        
        if (! $file = $em->find(File::class, $id)) {
            $file = $em->getRepository(File::class)->findOneBy(['path' => $id]);
        }

    	return $this->render("AppBundle:Media:show_file.html.twig", array(
    	    'file' => $file
    	));
    }
    
    
    /***
     * List folders
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listFoldersAction(Request $request) {
        return $this->render("AppBundle:Media:list_folders.html.twig", [
            'folders' => $this->getDoctrine()->getRepository(Folder::class)->findBy($_GET, ['createdAt' => 'DESC'])
        ]);
    }
    
    
    /***
     * Show Folder
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFolderAction(Request $request, $id) {
        $em = $this->get('doctrine.orm.entity_manager');
        
        if (! $folder = $em->find(Folder::class, $id)) {
            $folder = $em->getRepository(Folder::class)->findOneBy(['slug' => $id]);
        }
        
        $files = null;
        if (! empty($folder->getFiles())) {
            $list = $this->get('admin.util.doctrine_query_parameter')->formatForInClause($folder->getFiles());
            $dql   = "SELECT f FROM MediaBundle:File f WHERE f.id IN ".$list;
            $files = $em->createQuery($dql)->getResult();
        }
        
    	return $this->render("AppBundle:Media:show_folder.html.twig", array(
    	    'folder' => $folder,
    	    'files' => $files,
    	));
    }
    
    
    /**
     * List file comments
     *
     * @param Request $request
     * @param File $file
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCommentsAction(Request $request, File $file) {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository(Comment::class)->findBy(['file' => $file->getId()]);
        
        if ($request->isXmlHttpRequest() === true) {
            $array = [];
            
            if ($comments) {
                foreach ($comments as $comment) {
                    $item = [
                        'id' => $comment->getId(),
                        'created' => $comment->getCreatedAt()->format("Y-m-d H:i"),
                        'content' => $comment->getContent(),
                        'fullname' => $comment->getCreatedBy(),
                        'upvote_count' => $comment->getVotes() ? count($comment->getVotes()) : 0,
                        'user_has_upvoted' => $em->getRepository(CommentVote::class)->findOneBy([
                            'createdBy' => $this->getUser()->getFullName(),
                            'comment' => $comment->getId()
                        ]) ? true : false
                    ];
                    
                    if ($comment->getParentNode() !== null) {
                        $item['parent'] = $comment->getParentNode()->getId();
                    }
                    
                    $array[] = $item;
                }
            }
            
            return new JsonResponse($array);
        }
        
        return $this->render("AppBundle:Media:list_comments.html.twig", array('comments' => $comments));
    }
    
    /**
     * Add comment to file
     *
     * @param Request $request
     * @param File $file
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createCommentAction(Request $request, File $file) {
        $data = $request->request->all();
        
        $comment = new Comment();
        $comment->setVisible(true);
        $comment->setFile($file);
        $comment->setContent($data['content']);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        
        if (isset($data['parent'])) {
            $parent = $em->getRepository(Comment::class)->find($data['parent']);
            $comment->setChildNodeOf($parent);
        }
        
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse([
                'status' => true,
                'id' => $comment->getId(),
                'created' => $comment->getCreatedAt()->format("Y-m-d"),
                'parent' => $comment->getParentNode() !== null ? $comment->getParentNode()->getId() : "",
                'content' => $comment->getContent(),
                'fullname' => $this->getUser()->getFullName(),
                'upvote_count' => 0,
                'user_has_upvoted' => false
            ]);
        }
        
        return $this->redirectToRoute('app_media_file_show', ['id' => $file->getId()]);
    }
    
    /**
     * Update comment
     *
     * @param Request $request
     * @param File $file
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateCommentAction(Request $request, Comment $comment) {
        $data = $request->request->all();
        $comment->setVisible(true);
        $comment->setContent($data['content']);
        
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse([
                'status' => true,
                'id' => $comment->getId(),
                'created' => $comment->getCreatedAt()->format("Y-m-d"),
                'parent' => $comment->getParentNode() !== null ? $comment->getParentNode()->getId() : "",
                'content' => $comment->getContent(),
                'fullname' => $comment->getCreatedBy(),
                'upvote_count' => $comment->getVotes() ? count($comment->getVotes()) : 0,
                'user_has_upvoted' => $em->getRepository(CommentVote::class)->findOneBy([
                    'createdBy' => $this->getUser()->getFullName(),
                    'comment' => $comment->getId()
                ]) ? true : false
            ]);
        }
        
        return $this->redirectToRoute('app_media_file_show', ['id' => $comment->getFile()->getId()]);
    }
    
    /**
     * Delete comment
     *
     * @param Request $request
     * @param File $file
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteCommentAction(Request $request, Comment $comment) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(null, 204);
        }
        
        return $this->redirectToRoute('app_media_file_show', ['id' => $comment->getFile()->getId()]);
    }
    
    
    /**
     * Vote comment
     *
     * @param Request $request
     * @param File $file
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createCommentVoteAction(Request $request, Comment $comment) {
        $vote = new CommentVote();
        $vote->setComment($comment);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($vote);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($vote);
        }
        
        return $this->redirectToRoute('app_media_file_show', ['id' => $comment->getFile()->getId()]);
    }
    
    /**
     * Vote comment
     *
     * @param Request $request
     * @param File $file
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteCommentVoteAction(Request $request, Comment $comment) {
        $em = $this->getDoctrine()->getManager();
        
        $fileId = $comment->getFile()->getId();
        $vote = $em->getRepository(CommentVote::class)->findOneBy([
            'createdBy' => $this->getUser()->getFullName(),
            'comment' => $comment->getId()
        ]);
        
        if ($vote) {
            $em->remove($vote);
            $em->flush();
        }
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(null, 204);
        }
        
        return $this->redirectToRoute('app_media_file_show', ['id' => $fileId]);
    }
}
