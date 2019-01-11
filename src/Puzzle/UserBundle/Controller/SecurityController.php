<?php

namespace Puzzle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Puzzle\UserBundle\UserEvents;
use Puzzle\UserBundle\Entity\User;
use Puzzle\UserBundle\Event\UserEvent;

/**
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 */
class SecurityController extends Controller
{
	/**
	 * Login
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function loginAction(Request $request)
	{
	    if ($request->getHost() === $this->getParameter("admin_host") || $request->query->get('scope') === 'admin'){
	        return $this->redirect($this->generateUrl('admin_login'));
	    }
	    
	    return $this->forward('UserBundle:Security:loginForm', ['template' => "AppBundle:Security:login.html.twig"]);
	}
	
	/**
	 * Admin Login
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function adminLoginAction(Request $request){
	    return $this->forward('UserBundle:Security:loginForm', ['template' => "AdminBundle:Security:login.html.twig"]);
	}
	
	/**
	 * Login form
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function loginFormAction(Request $request, $template)
	{
	    /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
	    $session = $request->getSession();
	    
	    // get the error if any (works with forward and redirect -- see below)
	    if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
	        $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
	    } elseif (null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
	        $error = $session->get(Security::AUTHENTICATION_ERROR);
	        $session->remove(Security::AUTHENTICATION_ERROR);
	    } else {
	        $error = null;
	    }
	    
	    if (!$error instanceof AuthenticationException) {
	        $error = null; // The value does not come from the security component.
	    }
	    
	    // last username entered by the user
	    $lastUsername = (null === $session) ? '' : $session->get(Security::LAST_USERNAME);
	    
	    $csrfToken = $this->has('form.csrf_provider')
	    ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
	    : null;
	    
	    return $this->render($template, array(
	        'last_username'  => $lastUsername,
	        'error'          => $error,
	        'csrf_token'     => $csrfToken
	    ));
	}
	
	
	/**
	 * Logout Admin
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function logoutAction(Request $request)
	{
		$this->get('security.token_storage')->setToken(null);
		$request->getSession()->invalidate();
		
		$response = $this->redirectToRoute('login', array('scope' => $request->query->get('scope')));
		$response->headers->clearCookie('REMEMBERME');
		
		return $response;
	}
	
	/**
	 *
	 * Register
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function registrationAction(Request $request)
	{
		$data = $request->request->all();
		
		$user = new User();
		$em = $this->getDoctrine()->getManager();
		
		$user->setEmail($data['email']);
		$user->setUsername($data['email']);
		$user->setFirstName($data['first_name']);
		$user->setLastName($data['last_name']);
		$user->setPassword(hash('sha512', $data['password']));
		$user->setRoles(array("ROLE_USER"));
		
		$em->persist($user);
		$em->flush();
		
		/** User $user */
		$this->get('event_dispatcher')->dispatch(UserEvents::USER_PASSWORD, new UserEvent($user, [
		    'plainPassword' => $data['plainPassword']['first']
		]));
		
		$roles = $user->getRoles();
		
		if (!is_array($roles)) {
			$roles = array($roles);
		}
		
		$token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $roles);
		$this->get('security.token_storage')->setToken($token);
		$request->getSession()->set('_security_main', serialize($token));
		
		return $this->redirectToRoute('app_homepage');
	}
	
}
