# Puzzle project
# ============

Symfony project.

## **Installation**

---

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```yaml
composer require webundle/puzzle-web-apps
```

## **Default configurations**
### 
### **Step 1: Enable**
Enable admin bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Puzzle\AdminBundle\PuzzleAdminBundle(),
        );

        // ...
    }

    // ...
}
```

### **Step 2: Security**
Configure security by adding it in the `app/config/security.yml` file of your project:
```yaml
security:
    encoders:   
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
        - {path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - {path: ^/registration, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - {path: ^/connect, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - {path: ^%admin_prefix%, host: "%admin_host%", roles: ROLE_ADMIN }
```

### **Step 3: Register default routes**
Register default routes by adding it in the `app/config/routing.yml` file of your project:
```yaml
....
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
```

### **Step 4: Define hosts values**
Defined hosts values by adding it in the `app/config/parameters.yml` file of your project:
```yaml
...
host: <hostname>
admin_host: 'admin.%host%' # or %host% if you don't use subdomain
admin_prefix: '/' # or /admin/ if with you don't use subdomain
```


### **Step 5: Default configurations**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    website:
        title: 'Admin Puzzle' # Customize with your own admin name
        description: 'Lorem ipsum' # Customize with your own admin description
        email: 'johndoe@exemple.ci' # Customize with your own admin email
    time_format: "H:i" # customize time format
    date_format: "d-m-Y" # customize date format
    navigation:
        nodes:
            dashboard:
                label: 'dashboard.title'
                translation_domain: 'messages'
                path: admin_homepage
                attr:
                    class: 'fa fa-home' # customize icon class
                parent: ~
                user_roles: ['ROLE_ADMIN']
```

