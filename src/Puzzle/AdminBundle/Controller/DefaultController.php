<?php

namespace Puzzle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Puzzle\AdminBundle\Entity\Website;
use Puzzle\AdminBundle\Entity\Language;
use Puzzle\MediaBundle\Util\MediaUtil;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\AdminBundle\Service\Validator;
use Puzzle\AdminBundle\Entity\Menu;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Puzzle\AdminBundle\Form\Type\MenuType;
use Doctrine\ORM\EntityManager;
use Puzzle\AdminBundle\Form\Type\MenuItemType;
use Puzzle\AdminBundle\Entity\MenuItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Puzzle\AdminBundle\Form\Type\MenuItemTranslationType;
use Puzzle\AdminBundle\Entity\Module;
use Puzzle\UserBundle\Entity\User;
use Puzzle\AdminBundle\Form\Type\WebsiteType;

class DefaultController extends Controller
{
    public function indexAction(){
        return $this->render('AdminBundle:Default:index.html.twig');
    }
    
    /**
     * Settings
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function settingsAction(Request $request){
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_ADMIN", Validator::MANAGE);
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            
            $website = $em->getRepository(Website::class)->findOneBy([]);
            if ($website === null){
                $website = new Website();
            }
            
            $form = $this->createForm(WebsiteType::class, $website, [
                'method' => 'POST',
                'action' => $this->generateUrl('admin_settings')
            ]);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() === true && $form->isValid() === true) {
                $data = $request->request->all()['website_form'];
                // Add picture
                //....
                
                if (isset($data['locales']) && $data['locales']){
                    $locales = explode(',', $data['locales']);
                    foreach ($locales as $locale) {
                        $language = $em->getRepository(Language::class)->findOneBy(['name' => $locale]);
                        if ($language === null){
                            $language = new Language();
                            $language->setName($locale);
                            $em->persist($language);
                        }
                    }
                }
                
                $em->persist($website);
                $em->flush();
                
                if ($request->isXmlHttpRequest() === true) {
                    return new JsonResponse(['status' => true]);
                }
                
                return $this->redirectToRoute('admin_settings');
            }
            
            return $this->render("AdminBundle:Default:settings.html.twig", array(
                'form' => $form->createView()
            ));
        }catch (BadRequestHttpException $e){
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($e->getMessage(), 400);
            }
            
            throw new BadRequestHttpException($e->getMessage());
        }
    }
    
    
    /**
     * List modules
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listModulesAction(Request $request){
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_ADMIN", Validator::ACCESS);
            
            $modules = [];
            
            if ($request->get('type') == 'enable') {
                $modules = $this->getDoctrine()->getRepository("AdminBundle:Module")->findAll();
            }else{
                $list = $this->getParameter('admin.modules_available');
                $names = explode(',', $list);
                
                foreach ($names as $name){
                    $params = [
                        'id' => '',
                        'name' => $name,
                        'title' => $this->getParameter($name)['title'],
                        'description' => $this->getParameter($name)['description'],
                        'enable' => $this->getParameter($name)['enable'],
                        'icon' => $this->getParameter($name)['icon']
                    ];
                    
                    $module = $this->getDoctrine()->getRepository("AdminBundle:Module")->findOneBy(['name' => $name]);
                    if ($module !== null) {
                        $params['id'] = $module->getId();
                    }
                    
                    $modules[] = $params;
                }
            }
            
            return $this->render('AdminBundle:Default:list_modules.html.twig', ['modules' => $modules]);
        }catch (AccessDeniedHttpException $e){
            if ($request->isXmlHttpRequest() === true){
                return new JsonResponse($e->getMessage(), 403);
            }
            
            throw new AccessDeniedHttpException($e->getMessage());
        }
    }
    
    /**
     * Show module
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showModuleAction(Request $request, Module $module){
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_ADMIN", Validator::ACCESS);
            return $this->render("AdminBundle:Default:module.html.twig", [
                'module'    => $module,
                'roles'     => $this->getParameter($module->getName())['role']
            ]);
        }catch (AccessDeniedHttpException $e){
            if ($request->isXmlHttpRequest() === true){
                return new JsonResponse($e->getMessage(), 403);
            }
            
            throw new AccessDeniedHttpException($e->getMessage());
        }
    }
    
    /**
     * Install module
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function installModuleAction(Request $request){
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_ADMIN", Validator::MANAGE);
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $data = $request->request->all();
            
            $config = $this->getParameter($data['name']);
            $module = new Module();
            $module->setName($data['name']);
            $module->setTitle($config['title']);
            $module->setIcon($config['icon']);
            $module->setDescription($config['description']);
            $module->setEnable(true);
            
            $em->persist($module);
            
            if (isset($config['dependencies']) === true) {
                $dependencies = explode(',', $config['dependencies']);
                
                foreach ($dependencies as $name){
                    $dependency = $em->getRepository(Module::class)->findOneBy(['name' => $name]);
                    $config = $this->getParameter($name);
                    
                    if ($dependency === null) {
                        $dependency = new Module();
                        $dependency->setName($name);
                        $dependency->setTitle($config['title']);
                        $dependency->setIcon($config['icon']);
                        $dependency->setDescription($config['description']);
                        $dependency->setEnable(true);
                        
                        $em->persist($dependency);
                    }
                    
                    $module->addDependency($dependency->getId());
                    $dependency->addParent($module->getId());
                }
            }
            
            $em->flush();
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse(true);
            }
            
            return $this->redirectToRoute('admin_modules');
        }catch (BadRequestHttpException $e){
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($e->getMessage(), 400);
            }
            
            throw new BadRequestHttpException($e->getMessage());
        }
    }
    
    /**
     * Enable or disable module
     *
     * @param Request $request
     * @param Module $module
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function toggleModuleAction(Request $request, Module $module){
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_ADMIN", Validator::MANAGE);
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $enable = $module->getEnable() === true ? false : true;
            $module->setEnable($enable);
            $em->flush();
            
            return $this->redirectToRoute('admin_modules');
        }catch (BadRequestHttpException $e){
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($e->getMessage(), 400);
            }
            
            throw new BadRequestHttpException($e->getMessage());
        }
    }
    
    /**
     * Uninstall module
     *
     * @param Request $request
     * @param Module $module
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function uninstallAction(Request $request, Module $module){
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_ADMIN", Validator::MANAGE);
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            
            if ($request->isMethod('POST') === true){
                foreach ($module->getDependencies() as $dependencyId) {
                    $dependency = $em->getRepository("AdminBundle:Module")->find($dependencyId);
                    $dependency->removeParent($module->getId());
                }
                
                $em->remove($module);
                $em->flush();
                
                return $this->redirectToRoute('admin_modules');
            }
            
            $dependencies = $parents = [];
            
            foreach ($module->getDependencies() as $dependencyId){
                $dependency = $em->getRepository("AdminBundle:Module")->find($dependencyId);
                $dependencies[] = $dependency;
            }
            
            foreach ($module->getParents() as $parentId){
                $parent = $em->getRepository("AdminBundle:Module")->find($parentId);
                $parents[] = $parent;
            }
            
            return $this->render("AdminBundle:Module:remove.html.twig", [
                'module'        => $module,
                'dependencies'  => $dependencies,
                'parents'       => $parents
            ]);
        }catch (BadRequestHttpException $e){
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($e->getMessage(), 400);
            }
            
            throw new BadRequestHttpException($e->getMessage());
        }
    }
    
    /**
     * List permissions
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPermissionsAction(Request $request){
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_ADMIN", Validator::ACCESS);
            $modules = $this->getDoctrine()->getRepository(Module::class)->findAll();
            
            $permissions = [];
            foreach ($modules as $module) {
                $permissions[$module->getName()] = $this->getParameter($module->getName());
            }
            
            return $this->render('AdminBundle:Default:list_permissions.html.twig', ['modules' => $permissions]);
        }catch (AccessDeniedHttpException $e){
            if ($request->isXmlHttpRequest() === true){
                return new JsonResponse($e->getMessage(), 403);
            }
            
            throw new AccessDeniedHttpException($e->getMessage());
        }
    }
    
    /**
     * Attribute permission to users
     *
     * @param Request $request
     * @param string $module
     * @param string $roleKey
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function attributePermissionAction(Request $request, $module, $roleKey){
        try {
            /** @var Validator $validator */
            $validator = $this->get('admin.validator');
            $validator->isGranted($this->getUser(), "ROLE_ADMIN", Validator::MANAGE);
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            if ($request->isMethod("POST") === true) {
                $data = $request->request->all();
                $usersId = $data['usersId'];
                
                if ($usersId !== null) {
                    foreach ($usersId as $userId) {
                        $user = $em->getRepository(User::class)->findOneBy(['id' => $userId]);
                        if ($user !== null) {
                            $user->addRole($this->getParameter($module)['roles'][$roleKey]['label']);
                        }
                    }
                }
                
                $em->flush();
                
                if ($request->isXmlHttpRequest() === true) {
                    return new JsonResponse(['status' => true]);
                }
                
                return $this->redirectToRoute('admin_permissions');
            }
            
            return $this->render("AdminBundle:Default:attribute_permission.html.twig", [
                'module'    => $module,
                'roleKey'   => $roleKey,
                'role'      => $this->getParameter($module)['roles'][$roleKey]
            ]);
        }catch (BadRequestHttpException $e){
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($e->getMessage(), 400);
            }
            
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
