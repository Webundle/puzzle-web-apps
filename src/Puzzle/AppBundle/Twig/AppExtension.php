<?php
namespace Puzzle\AppBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 *
 * @author AGNES Gnagne Cedric <ccecenho55@gmail.com>
 *
 */
class AppExtension extends \Twig_Extension
{
	/**
	 * @var UrlGeneratorInterface $router
	 */
	protected $router;
	
	/**
	 * @var RequestStack $requestStack
	 */
	protected $requestStack;
	
	/**
	 * @var string $currentRoute
	 */
	protected $currentRoute;
	
	/**
	 * @var AuthorizationCheckerInterface $authorizationChecker
	 */
	protected $authorizationChecker;
	
	/**
	 * @var TranslatorInterface $translator
	 */
	protected $translator;
	
	/**
	 * @var array $config
	 */
	protected $config;
	
	/**
	 * @var array $navigationNodes
	 */
	protected $navigationNodes;
	
		
	public function __construct(UrlGeneratorInterface $router, RequestStack $requestStack, AuthorizationCheckerInterface $authorizationChecker, TranslatorInterface $translator, array $config) {
		$this->router = $router;
		$this->requestStack = $requestStack;
		$this->authorizationChecker = $authorizationChecker;
		$this->translator = $translator;
		$this->config = $config;
		$this->navigationNodes = $config['navigation']['nodes'];
	}
	
	public function getFunctions() {
		return [
    	    new \Twig_SimpleFunction('render_app_navigation', [$this, 'renderNavigationBlock'], ['needs_environment' => false, 'is_safe' => ['html']]),
    	    new \Twig_SimpleFunction('render_app_head_meta', [$this, 'renderHeadMeta'], ['needs_environment' => false, 'is_safe' => ['html']]),
		    new \Twig_SimpleFunction('render_app_head_title', [$this, 'renderHeadTitle'], ['needs_environment' => false, 'is_safe' => ['html']]),
		    new \Twig_SimpleFunction('render_app_website_infos', [$this, 'renderWebsiteInfos'], ['needs_environment' => false, 'is_safe' => ['html']]),
		];
	}
	
	/**
	 * @param \Twig_Environment $env
	 * @return string
	 */
	public function renderNavigationBlock() :string {
		$request = $this->requestStack->getCurrentRequest();
		$currentPath = $request->attributes->get('_route');
		
		$tree = $this->bildMenuTree($this->navigationNodes);
		$menu = '<div class="nav-wrap"><nav id="mainnav" class="mainnav"><ul class="menu">';
		
		foreach ($tree as $key => $node) {
			$menu .= $this->createMenuNode($key, $node, $currentPath);
		}
		
		$menu .= '</ul></nav></div>';
		
		return $menu;
	}
	
	protected function createMenuNode(string $nodeName, array $node, string $currentPath) {
		$children = '';
		$toActivate = false;
		
		if (!empty($node['children'])) {			
			foreach ($node['children'] as $key => $nodeItem) {
				if (!empty($nodeItem['children'])) {
					$children .= $this->createMenuNode($key, $nodeItem, $currentPath);
					if (false === $toActivate) {
					    foreach ($nodeItem['children'] as $nodeChildren) {
					        $subPaths = $nodeChildren['sub_paths'] ?? [];
					        if ($currentPath === $nodeChildren['path'] || true === in_array($currentPath, $subPaths)) {
					            $toActivate = true;
					            break;
					        }
					    }
					}
				} else {
					$children .= $this->createMenuNodeItem($nodeItem, $currentPath);
					
					if (false === $toActivate) {
					    $toActivate = $currentPath === $nodeItem['path'];
					}
					
					if (false === $toActivate && !empty($nodeItem['sub_paths'])) {
					    $toActivate = true === in_array($currentPath, $nodeItem['sub_paths']);
					}
				}
			}
			
			$children = '<ul class="sub-menu"'.(true === $toActivate ? '' : '').'>'.$children.'</ul>';
		}
		
		$menu = $this->createMenuNodeItem($node, $currentPath, $children, $toActivate);
		
		return $menu;
	}
	
	protected function createMenuNodeItem(array $nodeItem, string $currentPath, string $childrenMenu = '', bool $toActivate = false) {
		$label = $this->translator->trans($nodeItem['label'], $nodeItem['translation_parameters'], $nodeItem['translation_domain'], $nodeItem['translation_locale']);
		$class = isset($nodeItem['attr']['class']) ? $nodeItem['attr']['class'] : '';
		
		if ($childrenMenu !== '') {
			$liAttr = $toActivate ? ' class="active"' : '';
			$path = '#';			
		} else {
		    $liAttr = ($currentPath === $nodeItem['path'] || in_array($currentPath, $nodeItem['sub_paths'])) ? ' class="active"' : '';
			$path = $nodeItem['path'] ? $this->router->generate($nodeItem['path']) : '#';
		}
		
		return sprintf('<li%s><a href="%s">%s</a>%s</li>', $liAttr, $path, strtoupper($label), $childrenMenu);
	}
	
	/**
	 * @param array $nodes
	 * @param string $parent
	 * @return []
	 */
	protected function bildMenuTree(array $nodes, string $parent = null) {
		$tree = [];
		
		foreach ($nodes as $key => $node) {
			if ($node['parent'] !== $parent) {
				continue;
			}
			
			if (!empty($node['user_roles']) && false === $this->authorizationChecker->isGranted($node['user_roles'])) {
				continue;
			}
			
			unset($nodes[$key]);
			$tree[$key] = $node;
			$tree[$key]['children'] = $this->bildMenuTree($nodes, $key);
		}
		
		return $tree;
	}
	
	public function renderWebsiteInfos() {
	    return $this->config['website'];
	}
	
	public function renderHeadTitle() {
	    return $this->config['website']['title'];
	}
	
	public function renderHeadMeta() {
	    $meta = '<meta http-equiv="X-UA-Compatible"  content="IE=edge">';
	    $meta .= '<meta content="width=device-width, initial-scale=1.0" name="viewport"/>';
	    $meta .= '<meta http-equiv="Content-type"     content="text/html; charset=utf-8">';
	    $meta .= '<meta name="description" content="'.$this->config['website']['description'].'">';
	    
	    return $meta;
	}
	
	public function renderFacebookMeta() {
	    $website = $this->config['website'];
// 	    $meta = '<meta property="og:url" content="'.$website['og']['url'].'">';
	    $meta .= '<meta property="og:type" content="'.$website['type'].'">';
	    $meta .= '<meta property="og:title" content="'.$website['title'].'">';
	    $meta .= '<meta property="og:description" content="'.$website['description'].'">';
// 	    $meta .= '<meta property="og:image" content="'.$website['image'].'">';
	    
	    return $meta;
	}
}
