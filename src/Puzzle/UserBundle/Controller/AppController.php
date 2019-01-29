<?php

namespace Puzzle\UserBundle\Controller;

use Puzzle\UserBundle\Entity\User;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\UserBundle\UserEvents;
use Puzzle\UserBundle\Event\UserEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Puzzle\UserBundle\Form\Type\UserChangeSettingsType;
use Puzzle\UserBundle\Form\Type\UserChangePasswordType;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as AnnotationSecurity;
use Symfony\Component\Security\Core\Security;
use Puzzle\UserBundle\Form\Type\UserRegisterType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Puzzle\UserBundle\Form\Type\UserResetPasswordType;

class AppController extends Controller
{
    /**
     * Login
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        if ($request->getHost() === $this->getParameter("admin_host") || $request->query->get('scope') === 'admin') {
            return $this->redirect($this->generateUrl('admin_login'));
        }
        
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
        
        return $this->render('AppBundle:User:login.html.twig', array(
            'last_username'  => $lastUsername,
            'error'          => $error,
            'csrf_token'     => $csrfToken
        ));
    }
    
    /***
     * Create User
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request){
        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user, [
            'method' => 'POST',
            'action' => $this->generateUrl('register')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['app_user_register'];
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            // Update password
            if (isset($data['plainPassword']['first']) === true && $data['plainPassword']['first'] !== "") {
                /** User $user */
                $this->get('event_dispatcher')->dispatch(UserEvents::USER_PASSWORD, new UserEvent($user, [
                    'plainPassword' => $data['plainPassword']['first']
                ]));
            }
            
            $em->flush();
            return $this->redirectToRoute('show_registration_confirmation_email', ['email' => $user->getEmail()]);
        }
        
        return $this->render("AppBundle:User:register.html.twig", [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @param Request $request
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showRegistrationConfirmationEmailAction(Request $request, $email) {
        return $this->render('AppBundle:User:show_registration_confirmation_email.html.twig', ['email' => (string) $email]);
    }
    
    /**
     * Resetting reset user password: send email
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function resettingSendEmailAction(Request $request) {
        if ($username = $request->request->get('username')) {
            /** @var \Doctrine\ORM\EntityManager $em */
            $em = $this->get('doctrine.orm.default_entity_manager');
            /** @var \ApiBundle\Repository\UserRepository $er */
            $er = $em->getRepository(User::class);
            /** @var User $user */
            $user = $er->loadUserByUsername($username);
            
            if (null !== $user && false === $user->isPasswordRequestNonExpired($this->getParameter('app.resetting.retry_ttl'))) {
                if (null === $user->getConfirmationToken()) {
//                     $user->setConfirmationToken(TokenGenerator::generate(12));
                }
                
                /** @var \Symfony\Component\Translation\TranslatorInterface $translator */
                $translator = $this->get('translator');
                $subject = $translator->trans('app.user.resetting.email.subject', [], 'app');
                $body = $this->renderView('AppBundle:User:user_resetting_email.txt.twig', [
                    'username' => $username,
                    'confirmationUrl' => $this->generateUrl('app_user_resetting_reset', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL)
                ]);
                $this->sendEmail($this->getParameter('app.resetting.address'), $user->getEmail(), $subject, $body);
                
                $user->setPasswordRequestedAt(new \DateTime());
                $em->flush();
            }
        }
        
        return new RedirectResponse($this->generateUrl('app_user_resetting_check_email', ['username' => $username]));
    }
    
    /**
     * Resetting reset user password: check email
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resettingCheckEmailAction(Request $request) {
        if (!$request->query->get('username')) {
            return new RedirectResponse($this->generateUrl('app_user_resetting_request'));
        }
        
        return $this->render('AppBundle:User:resetting_check_email.html.twig', [
            'tokenLifetime' => ceil($this->getParameter('app.resetting.retry_ttl') / 3600)
        ]);
    }
    
    /**
     * Resetting reset user password: show form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resettingResetAction(Request $request, $token) {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');
        /** @var \ApiBundle\Repository\UserRepository $er */
        $er = $em->getRepository(User::class);
        
        /** @var User $user */
        if (!$user = $er->findOneBy(['confirmationToken' => $token])) {
            throw $this->createNotFoundException();
        }
        
        $form = $this->createForm(UserResetPasswordType::class, $user, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_user_resetting_reset', ['token' => $token])
        ]);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPasswordRequestedAt(null);
            $user->setConfirmationToken(null);
            $user->setPasswordChanged(true);
            $user->setEnabled(true);
            $em->flush();
            
            return $this->redirect($this->generateUrl('login'));
        }
        
        return $this->render('AppBundle:User:resetting_reset.html.twig', ['form' => $form->createView()]);
    }
    
    private function sendEmail($from, $to, string $subject, string $body) {
        $message = \Swift_Message::newInstance()
        ->setFrom($from)
        ->setTo($to)
        ->setSubject($subject)
        ->setBody($body);
        
        $this->get('mailer')->send($message);
    }
    
    public function confirmUserRegistrationAction(Request $request, $token) {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');
        /** @var \ApiBundle\Repository\UserRepository $er */
        $er = $em->getRepository(User::class);
        
        /** @var User $user */
        if (!$user = $er->findOneBy(['confirmationToken' => $token])) {
            throw $this->createNotFoundException();
        }
        
        $this->saveTargetPath($request->getSession(), 'app', $this->generateUrl('app_user_change_password', [], UrlGeneratorInterface::ABSOLUTE_URL));
        $user->setConfirmationToken(null);
        $user->setEnabled(true);
        $em->flush();
        
        return $this->render('AppBundle:User:confirm_registration.html.twig', ['username' => (string) $user]);
    }
    
    /**
     * @param Request $request
     * AnnotationSecurity("has_role('ROLE_USER')")
     */
    public function showUserProfileAction(Request $request) {
        return $this->render('AppBundle:User:show_user_profile.html.twig', ['user' => $this->getUser()]);
    }
    
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * AnnotationSecurity("has_role('ROLE_USER')")
     */
    public function updateUserSettingsAction(Request $request) {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $form = $this->createForm(UserChangeSettingsType::class, $currentUser, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_user_update_settings')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Doctrine\ORM\EntityManager $em */
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->flush();
            
            return $this->redirect($this->generateUrl('app_user_show_profile'));
        }
        
        return $this->render('AppBundle:User:update_user_settings.html.twig', ['form' => $form->createView()]);
    }
    
    /**
     * @param Request $request
     * AnnotationSecurity("is_granted('IS_AUTHENTICATED_FULLY') and has_role('ROLE_USER')")
     */
    public function changeUserPasswordAction(Request $request) {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $isPasswordChanged = $currentUser->isPasswordChanged();
        $form = $this->createForm(UserChangePasswordType::class, $currentUser, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_user_change_password')
        ]);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all()['user_change_password'];
            /** @var \Doctrine\ORM\EntityManager $em */
            $em = $this->get('doctrine.orm.default_entity_manager');
            
            // Update password
            if (isset($data['plainPassword']['first']) === true && $data['plainPassword']['first'] !== "") {
                /** User $user */
                $this->get('event_dispatcher')->dispatch(UserEvents::USER_PASSWORD, new UserEvent($user, [
                    'plainPassword' => $data['plainPassword']['first']
                ]));
            }
            
            $currentUser->setPasswordRequestedAt(null);
            $currentUser->setConfirmationToken(null);
            $currentUser->setPasswordChanged(true);
            $em->flush();
            
            if ($uri = $request->getSession()->get('change_password.on_success.redirect_to')) {
                $request->getSession()->remove('change_password.on_success.redirect_to');
            } else {
                $uri = $this->generateUrl('app_user_show_profile');
            }
            
            return $this->redirect($uri);
        }
        
        return $this->render('AppBundle:User:change_user_password.html.twig', [
            'form' => $form->createView(),
            'isPasswordChanged' => $isPasswordChanged
        ]);
    }
    
    /***
     * Update user picture
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateUserPictureAction(Request $request) {
        $user = $this->getUser();
        if ($request->isMethod('POST') === true) {
            $picture = $request->request->get('picture');
            
            if ($user->getPicture() === null || $user->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(User::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($user) {
                        $user->setPicture($filename);
                    }
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            return $this->redirectToRoute('app_user_show_profile', ['id' => $user->getId()]);
        }
        
        return $this->render("AdminBundle:User:update_user_picture.html.twig", [
            'user' => $user,
        ]);
    }
}
