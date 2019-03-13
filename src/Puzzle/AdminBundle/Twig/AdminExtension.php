<?php
namespace Puzzle\AdminBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Puzzle\AdminBundle\Util\DateUtil;

/**
 *
 * @author AGNES Gnagne Cedric <ccecenho55@gmail.com>
 *
 */
class AdminExtension extends \Twig_Extension
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
    	    new \Twig_SimpleFunction('render_admin_navigation', [$this, 'renderNavigationBlock'], ['needs_environment' => false, 'is_safe' => ['html']]),
    	    new \Twig_SimpleFunction('render_admin_head_meta', [$this, 'renderHeadMeta'], ['needs_environment' => false, 'is_safe' => ['html']]),
    	    new \Twig_SimpleFunction('render_admin_head_title', [$this, 'renderHeadTitle'], ['needs_environment' => false, 'is_safe' => ['html']]),
		    new \Twig_SimpleFunction('render_admin_dashboard', [$this, 'renderDashboardBlock'], ['needs_environment' => false, 'is_safe' => ['html']]),
		    new \Twig_SimpleFunction('date_time_ago', [$this, 'dateToTimeAgo'], ['needs_environment' => false, 'is_safe' => ['html']])
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
		$menu = '<ul>';
		
		foreach ($tree as $key => $node) {
			$menu .= $this->createMenuNode($key, $node, $currentPath);
		}
		
		$menu .= '</ul>';
		
		return $menu;
	}
	
	
	public function renderDashboardBlock() :string {
	    $tree = $this->bildMenuTree($this->navigationNodes);
	    $menu = '<ul class="md-list md-list-addon">';
	    
	    foreach ($tree as $key => $node) {
	        if ($key !== 'dashboard') {
    	        $label = $this->translator->trans($node['label'], $node['translation_parameters'], $node['translation_domain'], $node['translation_locale']);
    	        $description = $this->translator->trans($node['description'], $node['translation_parameters'], $node['translation_domain'], $node['translation_locale']);
    	        $icon = isset($node['attr']['class']) ? $node['attr']['class']:'';
    	        $path = $node['path'] ? $this->router->generate($node['path']) : '#';
    	        
                if (!empty($node['children'])) {
                    $currentNode = array_values($node['children'])[0];
                    $path = $currentNode['path'] ? $this->router->generate($currentNode['path']) : '#';
                }
                
                $menu .= sprintf('<li>
                                        <a href="%s" class="m-nav__link">
                                            <div class="md-list-addon-element"><i class="md-list-addon-icon %s"></i></div>
                                            <div class="md-list-content">
                                                <span class="md-list-heading">%s</span><br/>
                                                <span class="uk-text-small uk-text-muted">%s</span>
                                            </div>
                                        </a>
                                    </li>'
                    , $path, $icon, $label, $description);
	       }
	    }
	    
	    $menu .= '</ul>';
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
		    
		    $children = '<ul'.(true === $toActivate ? '' : '').'>'.$children.'</ul>';
		}
		
		$menu = $this->createMenuNodeItem($node, $currentPath, $children, $toActivate);
		
		return $menu;
	}
	
	protected function createMenuNodeItem(array $nodeItem, string $currentPath, string $childrenMenu = '', bool $toActivate = false) {
		$label = $this->translator->trans($nodeItem['label'], $nodeItem['translation_parameters'], $nodeItem['translation_domain'], $nodeItem['translation_locale']);
		$description = $this->translator->trans($nodeItem['description'], $nodeItem['translation_parameters'], $nodeItem['translation_domain'], $nodeItem['translation_locale']);
		$class = isset($nodeItem['description']) && $nodeItem['description'] !== null ? $this->translator->trans($nodeItem['description'], $nodeItem['translation_parameters'], $nodeItem['translation_domain'], $nodeItem['translation_locale']) : $label;
		$class = isset($nodeItem['attr']['class']) ? $nodeItem['attr']['class'] : '';
		
		if ($childrenMenu !== '') {
			$liAttr = ' class="submenu_trigger ' . ($toActivate ? 'current_section act_section' : '') . '" title="'.$description.'"';
			$path = '#';			
		} else {
		    $liAttr = ($currentPath === $nodeItem['path'] || in_array($currentPath, $nodeItem['sub_paths'])) ? ' class=" act_item"' : '';
		    $liAttr .= ' title="'.$description.'"';
			$path = $nodeItem['path'] ? $this->router->generate($nodeItem['path']) : '#';
		}
		
		if ($class !== '') {
		    $aContent = sprintf('<span class="menu_icon"><i class="%s" aria-hidden="true"></i></span><span class="menu_title">%s</span>', $class, $label);
		}else {
		    $aContent = $label;
		}
		
		return sprintf('<li%s><a href="%s">%s</a>%s</li>', $liAttr, $path, $aContent, $childrenMenu);
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
	
	public function renderHeadTitle() {
	    return $this->config['website']['title'];
	}
	
	public function renderHeadMeta() {
// 	    $website = $this->config['website'];
	    $meta = '<meta charset="UTF-8" />';
	    
	    return $meta;
	}
	
	public function dateToTimeAgo($date) {
	    $request = $this->requestStack->getCurrentRequest();
	    $locale = $request->getLocale();
	    
	    return DateUtil::timeAgo($date, $locale);
	}
}
