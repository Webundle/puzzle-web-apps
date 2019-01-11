<?php

namespace Puzzle\AdminBundle;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
final class AdminEvents
{
    /**
     * The event listener method receives a Puzzle\AdminBundle\Event\AdminInstallationEvent instance
     * @var string
     */
    const ADMIN_INSTALLING = 'admin.installing';
    
    /**
     * The event listener method receives a Puzzle\AdminBundle\Event\AdminInstallationEvent instance
     * @var string
     */
    const ADMIN_INSTALLED = 'admin.installed';
    
    /**
     * The event listener method receives a Puzzle\AdminBundle\Event\AdminInstallationEvent instance
     * @var string
     */
    const ADMIN_INSTALLATION_ROLLBACK = 'admin.installation_rollback';
}