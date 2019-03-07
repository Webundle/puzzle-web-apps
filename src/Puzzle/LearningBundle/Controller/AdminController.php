<?php
namespace Puzzle\LearningBundle\Controller;

use Puzzle\LearningBundle\Entity\Category;
use Puzzle\LearningBundle\Entity\Comment;
use Puzzle\LearningBundle\Entity\Post;
use Puzzle\LearningBundle\Form\Type\CategoryCreateType;
use Puzzle\LearningBundle\Form\Type\CategoryUpdateType;
use Puzzle\LearningBundle\Form\Type\PostCreateType;
use Puzzle\LearningBundle\Form\Type\PostUpdateType;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Puzzle\LearningBundle\LearningEvents;
use Puzzle\LearningBundle\Event\PostEvent;

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
        return $this->render("AdminBundle:Learning:list_categories.html.twig", array(
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
        return $this->render("AdminBundle:Learning:show_category.html.twig", array(
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
            'action' => $this->generateUrl('admin_learning_category_create')
        ]);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_learning_category_create'];
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
            return $this->redirectToRoute('admin_learning_category_update', ['id' => $category->getId()]);
        }
            
        return $this->render("AdminBundle:Learning:create_category.html.twig", array(
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
            'action' => $this->generateUrl('admin_learning_category_update', ['id' => $category->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_learning_category_update'];
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
            return $this->redirectToRoute('admin_learning_category_update', ['id' => $category->getId()]);
        }
        
        return $this->render("AdminBundle:Learning:update_category.html.twig", array(
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
        return $this->redirectToRoute('admin_learning_category_list');
    }
    
    
    /***
     * Show Posts
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPostsAction(Request $request){
        return $this->render("AdminBundle:Learning:list_posts.html.twig", array(
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
            'action' => $this->generateUrl('admin_learning_post_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_learning_post_create'];
            
            $tags = $post->getTags() !== null ? explode(',', $post->getTags()) : null;
            $post->setTags($tags);
            
            $enableComments = $post->getEnableComments() == "on" ? true : false;
            $post->setEnableComments($enableComments);
            
            $em->persist($post);
            $em->flush();
            
            /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->get('event_dispatcher');
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            $source = $request->request->get('source-picture');
            if ($source === 'local' && $picture !== null) {
                $dispatcher->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Post::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($post) {$post->setPicture($filename);}
                ]));
            }else {
                $post->setPicture($picture);
            }
            
            $audio = $request->request->get('audio') !== null ? $request->request->get('audio') : $data['audio'];
            $source = $request->request->get('source-audio');
            if ($source === 'local' && $audio !== null) {
                $dispatcher->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $audio,
                    'context' => MediaUtil::extractContext(Post::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($post) {$post->setAudio($filename);}
                ]));
            }else {
                $post->setAudio($audio);
            }
            
            $video = $request->request->get('video') !== null ? $request->request->get('video') : $data['video'];
            $source = $request->request->get('source-video');
            if ($source === 'local' && $video !== null) {
                $dispatcher->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $video,
                    'context' => MediaUtil::extractContext(Post::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($post) {$post->setVideo($filename);}
                ]));
            }else {
                $post->setVideo($video);
            }
            
            $document = $request->request->get('document') !== null ? $request->request->get('document') : $data['document'];
            $source = $request->request->get('source-document');
            if ($source === 'local' && $document !== null) {
                $dispatcher->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $document,
                    'context' => MediaUtil::extractContext(Post::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($post) {$post->setDocument($filename);}
                ]));
            }else {
                $post->setDocument($document);
            }
            
            $dispatcher->dispatch(LearningEvents::CREATE_POST, new PostEvent($post));
            
            $message = $this->get('translator')->trans('success.post', [
                '%item%' => $post->getName()
            ], 'messages');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            return $this->redirectToRoute('admin_learning_post_update', ['id' => $post->getId()]);
        }
        
        return $this->render("AdminBundle:Learning:create_post.html.twig", array(
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
            'action' => $this->generateUrl('admin_learning_post_update', ['id' => $post->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['admin_learning_post_update'];
            
            $tags = $post->getTags() !== null ? explode(',', $post->getTags()) : null;
            $post->setTags($tags);
            
            $enableComments = $post->getEnableComments() == "on" ? true : false;
            $post->setEnableComments($enableComments);
            
            /** @var EventDispatcher $dispatcher */
            $dispatcher = $this->get('event_dispatcher');
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            if ($post->getPicture() === null || $post->getPicture() !== $picture) {
                $dispatcher->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Post::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($post) {$post->setPicture($filename);}
                ]));
            }
            
            $audio = $request->request->get('audio') !== null ? $request->request->get('audio') : $data['audio'];
            if ($post->getAudio() === null || $post->getAudio() !== $audio) {
                $dispatcher->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $audio,
                    'context' => MediaUtil::extractContext(Post::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($post) {$post->getAudio($filename);}
                ]));
            }
            
            $video = $request->request->get('video') !== null ? $request->request->get('video') : $data['video'];
            if ($post->getVideo() === null || $post->getVideo() !== $video) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $video,
                    'context' => MediaUtil::extractContext(Post::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($post) {$post->getVideo($filename);}
                ]));
            }
            
            $document = $request->request->get('document') !== null ? $request->request->get('document') : $data['document'];
            if ($post->getVideo() === null || $post->getVideo() !== $document) {
                $dispatcher->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $document,
                    'context' => MediaUtil::extractContext(Post::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($post) {$post->getVideo($filename);}
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
            return $this->redirectToRoute('admin_learning_post_update', ['id' => $post->getId()]);
        }
        
        return $this->render("AdminBundle:Learning:update_post.html.twig", array(
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
        return $this->redirectToRoute('admin_learning_post_list');
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
        
        return $this->render("AdminBundle:Learning:list_comments.html.twig", array(
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
        
        return $this->redirect($this->generateUrl('admin_learning_comment_list'));
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
        
        return $this->redirect($this->generateUrl('admin_learning_comment_list'));
    }
}
