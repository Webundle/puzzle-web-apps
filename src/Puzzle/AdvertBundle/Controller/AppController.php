<?php

namespace Puzzle\AdvertBundle\Controller;

use Puzzle\AdvertBundle\Entity\Category;
use Puzzle\AdvertBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\AdvertBundle\Entity\Postulate;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
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
        return $this->render("AppBundle:Advert:list_posts.html.twig", array(
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
    	return $this->render("AppBundle:Advert:show_post.html.twig", array(
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
        return $this->render("AppBundle:Advert:list_categories.html.twig", [
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
        $dql   = "SELECT p FROM AdvertBundle:Post p WHERE p.category = :category";
        $query = $em->createQuery($dql)->setParameter('category', $category->getId());
        
        $paginator  = $this->get('knp_paginator');
        $posts = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );
        
    	return $this->render("AppBundle:Advert:show_category.html.twig", array(
    	    'category' => $category,
    	    'posts' => $posts,
    	));
    }
    
    /**
     * Create postulate
     */
    public function createPostulateAction(Request $request, $id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $post = $em->find(Post::class, $id);
        $user = $this->getUser();
        
        if (! $postulate = $em->getRepository(Postulate::class)->findOneBy(['post' => $id, 'user' => $user->getId()])) {
            $data = $request->request->all();
            $file = null;
            
            if (count($_FILES) > 0) {
                $folder = $this->get('media.file_manager')->createFolder($data['context'], $user);
                $media = $this->get('media.upload_manager')->prepareUpload($_FILES, $folder, $user);
                $file = $media[0]->getPath();
            }
        
            $postulate = new Postulate();
            $postulate->setUser($user);
            $postulate->setPost($post);
            $postulate->setFile($file);
            $postulate->setDescription($data['description']);
            
            $em->persist($postulate);
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(null, 204);
            }
            
            $this->addFlash('success', $this->get('translator')->trans('app.advert.postulate.created', ['%postName%' => $post->getName()], 'app'));
            return $this->redirectToRoute('app_advert_post_show', ['id' => $id]);
        }
        
        $this->addFlash('success', $this->get('translator')->trans('app.advert.postulate.duplicated', ['%postName%' => $post->getName()], 'app'));
        return $this->redirectToRoute('app_advert_post_show', ['id' => $id]);
    }
}
