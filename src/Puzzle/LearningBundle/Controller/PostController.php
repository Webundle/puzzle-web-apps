<?php
namespace Puzzle\LearningBundle\Controller;

use Puzzle\AdminBundle\Service\Validator;
use Puzzle\LearningBundle\Entity\Archive;
use Puzzle\LearningBundle\Entity\Post;
use Puzzle\LearningBundle\Form\Type\PostCreateType;
use Puzzle\LearningBundle\Form\Type\PostUpdateType;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class PostController extends Controller
{
    /***
     * Show Posts
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_LEARNING') or has_role('ROLE_ADMIN')")
     */
    public function listAction(Request $request) {
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_LEARNING", Validator::ACCESS);
            
            
            return $this->render("LearningBundle:Post:list.html.twig", array(
                'posts' => $this->getDoctrine()->getRepository(Post::class)->findAll()
            ));
        }catch (AccessDeniedHttpException $e){
            if ($request->isXmlHttpRequest() === true){
                return new JsonResponse($e->getMessage(), 403);
            }
            
            throw new AccessDeniedHttpException($e->getMessage());
        }
    }
    
    /***
     * Create post
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request) {
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_LEARNING", Validator::ACCESS);
            
            
            
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
                
                $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
                $source = $request->request->get('source-picture');
                if ($source === 'local' && $picture !== null) {
                    $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
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
                    $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
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
                    $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
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
                    $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                        'path' => $document,
                        'context' => MediaUtil::extractContext(Post::class),
                        'user' => $this->getUser(),
                        'closure' => function($filename) use ($post) {$post->setDocument($filename);}
                    ]));
                }else {
                    $post->setDocument($document);
                }
                
                $now = new \DateTime();
                $archive = $em->getRepository("LearningBundle:Archive")->findOneBy([
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
                
                if ($request->isXmlHttpRequest() === true) {
                    return new JsonResponse(['status' => true]);
                }
                
                $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
                return $this->redirectToRoute('admin_learning_post_update', ['id' => $post->getId()]);
            }
            
            return $this->render("LearningBundle:Post:create.html.twig", array(
                'form' => $form->createView()
            ));
        }catch (AccessDeniedHttpException $e){
            if ($request->isXmlHttpRequest() === true){
                return new JsonResponse($e->getMessage(), 403);
            }
            
            throw new AccessDeniedHttpException($e->getMessage());
        }
    }
    
    
    /***
     * Update post
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_LEARNING') or has_role('ROLE_ADMIN')")
     */
    public function updateAction(Request $request, Post $post) {
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
            
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            if ($post->getPicture() === null || $post->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Post::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($post) {$post->setPicture($filename);}
                ]));
            }
            
            $audio = $request->request->get('audio') !== null ? $request->request->get('audio') : $data['audio'];
            if ($post->getAudio() === null || $post->getAudio() !== $audio) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
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
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $document,
                    'context' => MediaUtil::extractContext(Post::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($post) {$post->getVideo($filename);}
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            $this->addFlash('success', $this->get('translator')->trans('success.put', [], 'messages'));
            return $this->redirectToRoute('admin_learning_post_update', ['id' => $post->getId()]);
        }
        
        return $this->render("LearningBundle:Post:update.html.twig", array(
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
    public function deleteAction(Request $request, Post $post) {
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_LEARNING", Validator::ACCESS);
            
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(['status' => true]);
            }
            
            return $this->redirectToRoute('admin_learning_post_list');
        }catch (AccessDeniedHttpException $e){
            if ($request->isXmlHttpRequest() === true){
                return new JsonResponse($e->getMessage(), 403);
            }
            
            throw new AccessDeniedHttpException($e->getMessage());
        }
    }
}
