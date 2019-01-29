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
use Puzzle\UserBundle\Form\Type\UserRegisterType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Puzzle\UserBundle\Form\Type\UserResetPasswordType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * 
 * @author qwincy <qwincypercy@fermentuse.com>
 *
 */
class SecurityController extends Controller
{
	
    /**
     * @param Request $request
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirmEmailAction(Request $request, $email) {
        return $this->render('AppBundle:User:confirm_email.html.twig', ['email' => (string) $email]);
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
                $subject = $translator->trans('resetting.email.subject', [], 'app');
                $body = $this->renderView('AppBundle:User:resetting_email.txt.twig', [
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
            
            return $this->redirect($this->generateUrl('app_security_login'));
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
}
