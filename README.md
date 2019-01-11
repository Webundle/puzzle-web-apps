Puzzle project
==================

Symfony project.

Installation

Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

`composer require webundle/puzzle-web-apps`


Step2: Configure

`admin:
    website:
        title: 'Admin Puzzle' # Customize with your own admin name
        description: 'Lorem ipsum' # Customize with your own admin description
        email: 'johndoe@exemple.ci' # Customize with your own admin email
    time_format: "H:i"
    date_format: "d-m-Y"
    navigation:
        nodes:
            dashboard:
                label: 'dashboard.title'
                translation_domain: 'messages'
                path: admin_homepage
                attr:
                    class: 'fa fa-home'
                parent: ~
                user_roles: ['ROLE_ADMIN']
`

Step 3: Security

`
security:
    encoders:
    #     # Symfony\Component\Security\Core\User\User: plaintext    
         Puzzle\UserBundle\Entity\User:
             algorithm:        sha512
             encode_as_base64: false
             iterations:       1

    role_hierarchy:
        ROLE_ADMIN: ROLE_USER
        ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]

    providers:
        chain_provider:
            chain:
                provider: [in_memory, user_db]
        user_db:
             id: user.provider
        in_memory:
            memory:
                users:
                    user:  { password: userpass, roles: [ 'ROLE_USER' ] }
                    admin: { password: adminpass, roles: [ 'ROLE_ADMIN' ] }

    firewalls:
        # wsse_secured:
        #     pattern:   ^/news.*
        #     stateless:  true
        #     wsse:      { lifetime: 30 }
        #     # anonymous : true
        dev:
            pattern:  ^/(_(profiler|wdt)|css|images|js)/
            security: false

#        login:
#            pattern:  ^/demo/secured/login$
#            security: false
            
        login:
            pattern: ^/login$
            anonymous: ~

        registration:
            pattern: ^/registration$
            anonymous: ~

        connect:
            pattern: ^/connect
            anonymous: ~
            
        admin:
            entry_point: admin.security.authentication_entry_point
            pattern: '^%admin_prefix%'
            host: '%admin_host%'
            provider: chain_provider
            access_denied_handler: security.access_denied_handler
            form_login:
                check_path: login_check
                login_path: admin_login
                success_handler: security.authentication_success_handler
                failure_handler: security.authentication_failure_handler
                csrf_token_generator: security.csrf.token_manager
            logout:
                path: /logout
                target: login
                delete_cookies:
                    REMEMBERME: { path: null, domain: null}
            remember_me:
                secret: "%secret%"
                lifetime: 84400
                # path: admin_homepage
                domain: ~
                always_remember_me: true

        main:
            entry_point: security.authentication_entry_point
            pattern: '^/'
            host: '%host%'
            anonymous: ~
            provider: chain_provider
            access_denied_handler: security.access_denied_handler
            form_login:
                check_path: login_check
                login_path: login
                success_handler: security.authentication_success_handler
                failure_handler: security.authentication_failure_handler
                csrf_token_generator: security.csrf.token_manager
            logout:
                path: /logout
                target: app_homepage
            remember_me:
                secret: "%secret%"
                lifetime: 84400
                path: app_homepage
                domain: ~
                always_remember_me: true
        
        secured_area:
            pattern:    ^/demo/secured/
            form_login:
                check_path: _security_check
                login_path: _demo_login
            logout:
                path:   _demo_logout
                target: _demo
            #anonymous: ~
            #http_basic:
            #    realm: "Secured Demo Area"

    access_control:
        #- { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY, requires_channel: https }
        - {path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - {path: ^/registration, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - {path: ^/connect, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - {path: ^%admin_prefix%, host: "%admin_host%", roles: ROLE_ADMIN }
        # - {path: ^/, roles: ROLE_USER }
`

Step 4: Routing

`
app:
    resource: "@AppBundle/Resources/config/routing.yml"
    # prefix:   /{_locale}
    host: "%host%"
    options:
        expose: true
    # requirements:
    #     _locale: en|fr

admin:
    resource: "@AdminBundle/Resources/config/routing.yml"
    host: '%admin_host%'
    options:
        expose: true
        
_liip_imagine:
    resource: "@LiipImagineBundle/Resources/config/routing.yaml"

fos_js_routing:
    resource: "@FOSJsRoutingBundle/Resources/config/routing/routing.xml"

JMSJobQueueBundle:
    resource: "@JMSJobQueueBundle/Controller/"
    type: annotation
    prefix: /jobs

`

Step 5: Parameters

`
host: puzzle.ci
admin_host: 'admin.%host%'
admin_prefix: '/'
`

Blog Bundle
===========

Step 1: Enable

Then, enable the bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

`<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Puzzle\BlogBundle\PuzzleBlogBundle(),
        );

        // ...
    }

    // ...
}
`
Step 2: Register the Routes

Load the bundle's routing definition in the application (usually in the `app/config/routing.yml` file):

# app/config/routing.yml
`puzzle_blog:
    resource: "@PuzzleBlogBundle/Resources/config/routing.yml"`

Step 3: Configure

Then, enable management bundle via admin modules interface by adding it to the list of registered bundles in the `app/config/config.yml` file of your project under:

`admin:
    ...
    modules_available: '...,blog'
    navigation:
        nodes:
            ...
            # Blog
            blog:
                label: 'blog.title'
                description: 'blog.description'
                translation_domain: 'messages'
                attr:
                    class: 'fa fa-newspaper-o'
                parent: ~
                user_roles: ['ROLE_BLOG', 'ROLE_ADMIN']
            blog_post:
                label: 'blog.post.sidebar'
                description: 'blog.post.description'
                translation_domain: 'messages'
                path: 'admin_blog_post_list'
                sub_paths: ['admin_blog_post_create', 'admin_blog_post_update', 'admin_blog_post_show']
                parent: blog
                user_roles: ['ROLE_BLOG', 'ROLE_ADMIN']
            blog_category:
                label: 'blog.post.sidebar'
                description: 'media.folder.description'
                translation_domain: 'messages'
                path: 'admin_blog_category_list'
                sub_paths: ['admin_blog_category_create', 'admin_blog_category_update', 'admin_blog_category_show']
                parent: blog
                user_roles: ['ROLE_BLOG', 'ROLE_ADMIN']
`