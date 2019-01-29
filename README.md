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
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new JMS\JobQueueBundle\JMSJobQueueBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Knp\DoctrineBehaviors\Bundle\DoctrineBehaviorsBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
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
# Admin Configuration
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

# App Configuration
app:
    website:
        title: ''
        description: ''
        type: ''
        email: ''
        phoneNumber: ''
        contact: ''
    resetting:
        retry_ttl: 3600
        address: ''
    navigation:
        nodes:
            home:
                label: 'app.home.title'
                translation_domain: 'app'
                path: app_homepage
```


---

## **User Bundle**

---

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

            new Puzzle\UserBundle\UserBundle(),
        );

        // ...
    }

    // ...
}
```

### **Step 2: Register default routes**
Register default routes by adding it in the `app/config/routing.yml` file of your project:
```yaml
....
blog:
    resource: "@UserBundle/Resources/config/routing.yml"
    prefix:   /

```
See all user routes by typing: `php bin/console debug:router | grep user`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
    navigation:
        nodes:
            ...
            
            # User
            user:
                label: 'user.title'
                description: 'user.description'
                translation_domain: 'messages'
                attr:
                    class: 'fa fa-users'
                parent: ~
                user_roles: ['ROLE_ADMIN']
            user_list:
                label: 'user.account.sidebar'
                translation_domain: 'messages'
                path: 'admin_user_list'
                sub_paths: ['admin_user_create', 'admin_user_update', 'admin_user_show']
                parent: user
                user_roles: ['ROLE_ADMIN']
            user_group:
                label: 'user.group.sidebar'
                translation_domain: 'messages'
                path: 'admin_user_group_list'
                sub_paths: ['admin_user_group_create', 'admin_user_group_update', 'admin_user_group_show']
                parent: user
                user_roles: ['ROLE_ADMIN']
```

---

## **Media Bundle**

---

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

            new Puzzle\MediaBundle\MediaBundle(),
        );

        // ...
    }

    // ...
}
```

### **Step 2: Register default routes**
Register default routes by adding it in the `app/config/routing.yml` file of your project:
```yaml
....
blog:
    resource: "@MediaBundle/Resources/config/routing.yml"
    prefix:   /

```
See all media routes by typing: `php bin/console debug:router | grep media`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
    navigation:
        nodes:
            ...
            
            # Media
            media:
                label: 'media.title'
                description: 'media.description'
                translation_domain: 'messages'
                attr:
                    class: 'fa fa-cloud'
                parent: ~
                user_roles: ['ROLE_MEDIA', 'ROLE_ADMIN']
            media_file:
                label: 'media.file.sidebar'
                description: 'media.file.description'
                translation_domain: 'messages'
                path: 'admin_media_file_list'
                parent: media
                user_roles: ['ROLE_MEDIA', 'ROLE_ADMIN']
            media_folder:
                label: 'media.folder.sidebar'
                description: 'media.folder.description'
                translation_domain: 'messages'
                path: 'admin_media_folder_list'
                sub_paths: ['admin_media_folder_create', 'admin_media_folder_update', 'admin_media_folder_show']
                parent: media
                user_roles: ['ROLE_MEDIA', 'ROLE_ADMIN']
```


---

## **Blog Bundle**

---

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

            new Puzzle\BlogBundle\BlogBundle(),
        );

        // ...
    }

    // ...
}
```

### **Step 2: Register default routes**
Register default routes by adding it in the `app/config/routing.yml` file of your project:
```yaml
....
blog:
    resource: "@BlogBundle/Resources/config/routing.yml"
    prefix:   /

```
See all blog routes by typing: `php bin/console debug:router | grep blog`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
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
                label: 'blog.navigation.post'
                description: 'blog.post.description'
                translation_domain: 'messages'
                path: 'admin_blog_post_list'
                sub_paths: ['admin_blog_post_create', 'admin_blog_post_update', 'admin_blog_post_show']
                parent: blog
                user_roles: ['ROLE_BLOG', 'ROLE_ADMIN']
            blog_category:
                label: 'blog.navigation.category'
                description: 'blog.category.description'
                translation_domain: 'messages'
                path: 'admin_blog_category_list'
                sub_paths: ['admin_blog_category_create', 'admin_blog_category_update', 'admin_blog_category_show']
                parent: blog
                user_roles: ['ROLE_BLOG', 'ROLE_ADMIN']

```


---

## **Calendar Bundle**

---

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

            new Puzzle\CalendarBundle\CalendarBundle(),
            new Puzzle\SchedulingBundle\SchedulingBundle(),
        );

        // ...
    }

    // ...
}
```

### **Step 2: Register default routes**
Register default routes by adding it in the `app/config/routing.yml` file of your project:
```yaml
....
blog:
    resource: "@CalendarBundle/Resources/config/routing.yml"
    prefix:   /

```
See all caendar routes by typing: `php bin/console debug:router | grep calendar`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
    navigation:
        nodes:
            ...
            
            # Calendar
            calendar:
                label: 'calendar.title'
                description: 'calendar.description'
                translation_domain: 'messages'
                attr:
                    class: 'fa fa-calendar'
                parent: ~
                user_roles: ['ROLE_MEDIA', 'ROLE_ADMIN']
            calendar_agenda:
                label: 'calendar.agenda.sidebar'
                description: 'calendar.agenda.description'
                translation_domain: 'messages'
                path: 'admin_calendar_agenda_list'
                parent: calendar
                user_roles: ['ROLE_CALENDAR', 'ROLE_ADMIN']
            calendar_moment:
                label: 'calendar.moment.sidebar'
                description: 'calendar.moment.description'
                translation_domain: 'messages'
                path: 'admin_calendar_moment_list'
                sub_paths: ['admin_calendar_moment_create', 'admin_calendar_moment_update', 'admin_calendar_moment_show']
                parent: calendar
                user_roles: ['ROLE_CALENDAR', 'ROLE_ADMIN']
```

### **Step 4: Requirements**
For this bundle to work, you have to install [supervisord](http://supervisord.org/installing.html)


---

## **Contact Bundle**

---

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

            new Puzzle\ContactBundle\ContactBundle(),
        );

        // ...
    }

    // ...
}
```

### **Step 2: Register default routes**
Register default routes by adding it in the `app/config/routing.yml` file of your project:
```yaml
....
blog:
    resource: "@ContactBundle/Resources/config/routing.yml"
    prefix:   /

```
See all contact routes by typing: `php bin/console debug:router | grep contact`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
    navigation:
        nodes:
            ...
            # Contact
            contact:
                label: 'contact.title'
                description: 'contact.description'
                translation_domain: 'messages'
                attr:
                    class: 'fa fa-book'
                parent: ~
                user_roles: ['ROLE_CONTACT', 'ROLE_ADMIN']
            contact_list:
                label: 'contact.sidebar'
                translation_domain: 'messages'
                path: 'admin_contact_list'
                sub_paths: ['admin_contact_create', 'admin_contact_update']
                parent: contact
                user_roles: ['ROLE_CONTACT', 'ROLE_ADMIN']
            contact_group:
                label: 'contact.group.sidebar'
                translation_domain: 'messages'
                path: 'admin_contact_group_list'
                sub_paths: ['admin_contact_group_create', 'admin_contact_group_update']
                parent: contact
                user_roles: ['ROLE_CONTACT', 'ROLE_ADMIN']
            contact_request:
                label: 'contact.request.sidebar'
                translation_domain: 'messages'
                path: 'admin_contact_request_list'
                sub_paths: ['admin_contact_request_create', 'admin_contact_request_update']
                parent: contact
                user_roles: ['ROLE_CONTACT', 'ROLE_ADMIN']
```

