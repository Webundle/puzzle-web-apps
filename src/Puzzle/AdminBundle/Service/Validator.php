<?php
namespace Puzzle\AdminBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Puzzle\UserBundle\Entity\User;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class Validator {
    const ACCESS = 'access';
    const MANAGE = 'manage';
    
    /**
     * @var EntityManager $em
     */
    protected $em;
    
    /**
     * @var TranslatorInterface $translator
     */
    protected $translator;
    
    /**
     * @param EntityManager $em
     * @param string $defaultLocale
     */
    public function __construct(EntityManager $em, TranslatorInterface $translator){
        $this->em = $em;
        $this->translator = $translator; 
    }
    
    public function isGranted(User $user, $role, bool $isXmlHttpRequest = false, string $mode = self::MANAGE) {
        if ($user->hasRole($role) === false && $user->hasRole(User::ROLE_ADMIN) === false){
            if ($mode == self::ACCESS){
                $message = $this->translator->trans('error.403', [], 'error');
                throw new AccessDeniedHttpException($message);
            }
            
            $message = $this->translator->trans('error.400', [], 'error');
            throw new BadRequestHttpException($message);
        }
        
        return true;
    }
}