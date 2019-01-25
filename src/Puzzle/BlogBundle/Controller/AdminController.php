<?php
namespace Puzzle\BlogBundle\Controller;

use Puzzle\BlogBundle\Entity\Archive;
use Puzzle\BlogBundle\Entity\Category;
use Puzzle\BlogBundle\Entity\Comment;
use Puzzle\BlogBundle\Entity\Post;
use Puzzle\BlogBundle\Form\Type\CategoryCreateType;
use Puzzle\BlogBundle\Form\Type\CategoryUpdateType;
use Puzzle\BlogBundle\Form\Type\PostCreateType;
use Puzzle\BlogBundle\Form\Type\PostUpdateType;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class AdminController extends Controller
{
    /***
     * Show categories
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCategoriesAction(Request $request) {
        return $this->render("AdminBundle:Blog:list_categories.html.twig", array(
            'categories' => $this->getDoctrine()->getRepository(Category::class)->findAll()
        ));
    }
    
    
    /***
     * Show category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCategoryAction(Request $request, Category $category) {
        return $this->render("AdminBundle:Blog:show_category.html.twig", array(
            'category' => $category
        ));
    }
    
    
    /***
     * Create category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createCategoryAction(Request $request) {
        $category = new Category();
        $em = $this->getDoctrine()->getManager();
        $parentId = $request->query->get('parent');
        
        if ($parentId === true && $parent = $em->getRepository(Category::class)->find($parentId)){
            $category->setParentNode($parent);
        }
        
        $form = $this->createForm(CategoryCreateType::class, $category, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_blog_category_create')
        ]);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_blog_category_create'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Category::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($category) {
                        $category->setPicture($filename);
                    }
                 ]));
            }
            
            $em->persist($category);
            $em->flush();
            
            $message = $this->get('translator')->trans('success.post', [
                '%item%' => $category->getName()
            ], 'messages');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            return $this->redirectToRoute('admin_blog_category_update', ['id' => $category->getId()]);
        }
            
        return $this->render("AdminBundle:Blog:create_category.html.twig", array(
            'form' => $form->createView(),
            'parent' => $request->query->get('parent')
        ));
    }
    
    
    /***
     * Update category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateCategoryAction(Request $request, Category $category) {
        $form = $this->createForm(CategoryUpdateType::class, $category, [
            'method' => 'POST', 
            'action' => $this->generateUrl('admin_blog_category_update', ['id' => $category->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_blog_category_update'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($category->getPicture() === null || $category->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Category::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($category) {
                        $category->setPicture($filename);
                    }
                 ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $message = $this->get('translator')->trans('success.put', [
                '%item%' => $category->getName()
            ], 'messages');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            return $this->redirectToRoute('admin_blog_category_update', ['id' => $category->getId()]);
        }
        
        return $this->render("AdminBundle:Blog:update_category.html.twig", array(
            'category' => $category,
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Delete category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCategoryAction(Request $request, Category $category) {
        $message = $this->get('translator')->trans('success.delete', [
            '%item%' => $category->getName()
        ], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_blog_category_list');
    }
    
    
    /***
     * Show Posts
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPostsAction(Request $request){
        return $this->render("AdminBundle:Blog:list_posts.html.twig", array(
            'posts' => $this->getDoctrine()->getRepository(Post::class)->findAll()
        ));
    }
    
    /***
     * Create post
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createPostAction(Request $request) {
        $post = new Post();
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(PostCreateType::class, $post, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_blog_post_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_blog_post_create'];
            
            $tags = $post->getTags() !== null ? explode(',', $post->getTags()) : null;
            $post->setTags($tags);
            
            $enableComments = $post->getEnableComments() == "on" ? true : false;
            $post->setEnableComments($enableComments);
            
            $flashExpiresAt = isset($data['flash']) && $data['flash'] == true ? new \DateTime($data['flashExpiresAt']) : null;
            $post->setFlashExpiresAt($flashExpiresAt);
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Post::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($post) {$post->setPicture($filename);}
                ]));
            }
            
            $now = new \DateTime();
            $archive = $em->getRepository(Archive::class)->findOneBy([
                'month' => (int) $now->format("m"),
                'year' => $now->format("Y")
            ]);
            
            if ($archive === null) {
                $archive = new Archive();
                $archive->setMonth((int) $now->format("m"));
                $archive->setYear($now->format("Y"));
                
                $em->persist($archive);
            }
            
            $post->setArchive($archive);
            
            $em->persist($post);
            $em->flush();
            
            $message = $this->get('translator')->trans('success.post', [
                '%item%' => $post->getName()
            ], 'messages');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            return $this->redirectToRoute('admin_blog_post_update', ['id' => $post->getId()]);
        }
        
        return $this->render("AdminBundle:Blog:create_post.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Update post
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updatePostAction(Request $request, Post $post) {
        $form = $this->createForm(PostUpdateType::class, $post, [
            'method' => 'POST',
            'action' => $this->generateUrl('admin_blog_post_update', ['id' => $post->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_blog_post_update'];
            
            $tags = $post->getTags() !== null ? explode(',', $post->getTags()) : null;
            $post->setTags($tags);
            
            $enableComments = $post->getEnableComments() == "on" ? true : false;
            $post->setEnableComments($enableComments);
            
            $flashExpiresAt = isset($data['flash']) && $data['flash'] == true ? new \DateTime($data['flashExpiresAt']) : null;
            $post->setFlashExpiresAt($flashExpiresAt);
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($post->getPicture() === null || $post->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Post::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($post) {$post->setPicture($filename);}
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $message = $this->get('translator')->trans('success.put', [
                '%item%' => $post->getName()
            ], 'messages');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            return $this->redirectToRoute('admin_blog_post_update', ['id' => $post->getId()]);
        }
        
        return $this->render("AdminBundle:Blog:update_post.html.twig", array(
            'post' => $post,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete Post
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletePostAction(Request $request, Post $post) {
        $message = $this->get('translator')->trans('success.delete', [
            '%item%' => $post->getName()
        ], 'messages');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
        
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_blog_post_list');
    }
    
    
    /***
     * Show Comments
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCommentsAction(Request $request)
    {
        $comments = $this->getDoctrine()
                        ->getRepository(Comment::class)
                        ->findBy(['post' => $request->get('post')],['createdAt' => 'DESC']);
        
        return $this->render("AdminBundle:Blog:list_comments.html.twig", array(
            'comments' => $comments
        ));
    }
    
    /**
     * Update Comment
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateCommentAction(Request $request, $id)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comment::class)->find($id);
        
        if(isset($data['is_visible']) && $data['is_visible'] == "on"){
            $comment->setIsVisbile(true);
        }else{
            $comment->setIsVisbile(false);
        }
        
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(null, 204);
        }
        
        return $this->redirect($this->generateUrl('admin_blog_comment_list'));
    }
    
    
    /***
     * Delete comment
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCommentAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comment::class)->find($id);
        
        $em->remove($comment);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse(null, 204);
        }
        
        return $this->redirect($this->generateUrl('admin_blog_comment_list'));
    }
}
