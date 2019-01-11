<?php

namespace Puzzle\BlogBundle\Controller;

use Puzzle\BlogBundle\Entity\Category;
use Puzzle\BlogBundle\Entity\Comment;
use Puzzle\BlogBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\BlogBundle\Entity\CommentVote;

/**
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 */
class AppController extends Controller
{
    
    /**
     * List posts
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPostsAction(Request $request) {
        return $this->render("AppBundle:Blog:list_posts.html.twig", array(
            'posts' => $this->getDoctrine()->getRepository(Post::class)->findBy([], ['createdAt' => 'DESC'])
        ));
    }
    
    /**
     * Show Post
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showPostAction(Request $request, $slug) {
    	return $this->render("AppBundle:Blog:show_post.html.twig", array(
    	    'post' => $this->getDoctrine()->getRepository(Post::class)->findOneBy(['slug' => $slug])
    	));
    }
    
    
    /***
     * List categories
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCategoriesAction(Request $request) {
        return $this->render("AppBundle:Blog:list_categories.html.twig", [
            'categories' => $this->getDoctrine()->getRepository(Category::class)->findBy(['parent' => null], ['createdAt' => 'DESC'])
        ]);
    }
    
    
    /***
     * Show Category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCategoryAction(Request $request, Category $category) {
        $em    = $this->getDoctrine()->getManager();
        $dql   = "SELECT p FROM BlogBundle:Post p WHERE p.category = :category";
        $query = $em->createQuery($dql)->setParameter('category', $category->getId());
        
        $paginator  = $this->get('knp_paginator');
        $posts = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );
        
    	return $this->render("AppBundle:Blog:show_category.html.twig", array(
    	    'category' => $category,
    	    'posts' => $posts,
    	));
    }
    
    
    /**
     * List post comments
     * 
     * @param Request $request
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCommentsAction(Request $request, Post $post) {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository(Comment::class)->findBy(['post' => $post->getId()]);
        
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
            
        return $this->render("AppBundle:Blog:list_comments.html.twig", array('comments' => $comments));
    }
    
    /**
     * Add comment to post
     * 
     * @param Request $request
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createCommentAction(Request $request, Post $post) {
        $data = $request->request->all();
        
        $comment = new Comment();
        $comment->setVisible(true);
        $comment->setPost($post);
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
        
        return $this->redirectToRoute('app_blog_post_show', ['id' => $post->getId()]);
    }
    
    /**
     * Update comment
     *
     * @param Request $request
     * @param Post $post
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
        
        return $this->redirectToRoute('app_blog_post_show', ['id' => $comment->getPost()->getId()]);
    }
    
    /**
     * Delete comment
     *
     * @param Request $request
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteCommentAction(Request $request, Comment $comment) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(null, 204);
        }
        
        return $this->redirectToRoute('app_blog_post_show', ['id' => $comment->getPost()->getId()]);
    }
    
    
    /**
     * Vote comment
     *
     * @param Request $request
     * @param Post $post
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
        
        return $this->redirectToRoute('app_blog_post_show', ['id' => $comment->getPost()->getId()]);
    }
    
    /**
     * Vote comment
     *
     * @param Request $request
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteCommentVoteAction(Request $request, Comment $comment) {
        $em = $this->getDoctrine()->getManager();
        
        $postId = $comment->getPost()->getId();
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
        
        return $this->redirectToRoute('app_blog_post_show', ['id' => $postId]);
    }
}
