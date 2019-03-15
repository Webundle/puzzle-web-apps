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
                target: admin_homepage
                delete_cookies:
                    REMEMBERME: { path: null, domain: null}
            remember_me:
                secret: "%secret%"
                lifetime: 84400
                path: admin_homepage
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
                lifetime: 172 800 # 2 days
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
        - {path: ^%admin_prefix%, host: "%admin_host%", roles: ROLE_ADMIN }
        # - {path: ^/, roles: ROLE_USER }

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
imports:
    - { resource: parameters.yml }
    - { resource: security.yml }
    # - { resource: services.yml }

# Put parameters here that don't need to change on each machine where the app is deployed
# http://symfony.com/doc/current/best_practices/configuration.html#application-related-configuration
parameters:
    locale: fr

framework:
    #esi:             ~
    # default_locale: fr
    translator:
        fallbacks: ["%locale%"]

    secret:          "%secret%"
    router:
        resource: "%kernel.root_dir%/config/routing.yml"
        strict_requirements: ~
    form:            ~
    csrf_protection: ~
    validation:      { enable_annotations: true }
    #serializer:      { enable_annotations: true }
    templating:
        engines: ['twig']
    default_locale:  "%locale%"
    trusted_hosts:   ~
    trusted_proxies: ~
    session:
        # handler_id set to null will use default session handler from php.ini
        handler_id:  ~
    fragments:       ~
    http_method_override: true
    assets:
        version: 2
        version_format: '%%s?version=%%s'
        base_path: ~

# Twig Configuration
twig:
    debug:            "%kernel.debug%"
    strict_variables: "%kernel.debug%"
    paths:
        '%kernel.root_dir%/../web': template

# Assetic Configuration
assetic:
    debug:          "%kernel.debug%"
    use_controller: false
    # bundles:        [AppBundle]
    java: /usr/bin/java
    filters:
        cssrewrite: ~
        #closure:
        #    jar: "%kernel.root_dir%/Resources/java/compiler.jar"
        # yui_js:
        #    jar: "%kernel.root_dir%/Resources/java/yuicompressor-2.4.8.jar"
        # yui_css:
        #    jar: "%kernel.root_dir%/Resources/java/yuicompressor-2.4.8.jar"
# Doctrine Configuration
doctrine:
    dbal:
        driver:   pdo_mysql
        host:     "%database_host%"
        port:     "%database_port%"
        dbname:   "%database_name%"
        user:     "%database_user%"
        password: "%database_password%"
        charset:  UTF8
        # if using pdo_sqlite as your database driver:
        #   1. add the path in parameters.yml
        #     e.g. database_path: "%kernel.root_dir%/data/data.db3"
        #   2. Uncomment database_path in parameters.yml.dist
        #   3. Uncomment next line:
        #path:     "%database_path%"

    orm:
        auto_generate_proxy_classes: "%kernel.debug%"
        naming_strategy: doctrine.orm.naming_strategy.underscore
        auto_mapping: true
            # metadata_cache_driver: apc
        # result_cache_driver: 
        #     type: memcached
        #     host: localhost
        #     port: 11211
        #     instance_class: Doctrine\Common\Cache\MemcachedCache
        # query_cache_driver:
        #     type:                 array # Required
        #     host:                 ~
        #     port:                 ~
        #     instance_class:       ~
        #     class:                ~
        #     namespace:            ~
#        mappings:
#            translatable:
#                type: annotation
#                is_bundle: false
#                prefix: Gedmo\Translatable\Entity
#                dir: "%kernel.root_dir%/../vendor/gedmo/doctrine-extensions/lib/Gedmo/Translatable/Entity"
#                alias: GedmoTranslatable
#            gedmo_translator:
#                type: annotation
#                prefix: Gedmo\Translator\Entity
#                dir: "%kernel.root_dir%/../vendor/gedmo/doctrine-extensions/lib/Gedmo/Translator/Entity"
#                alias: GedmoTranslator # (optional) it will default to the name set for the mapping
#                is_bundle: false
#
#stof_doctrine_extensions:
#    default_locale: "%locale%"
#    translation_fallback: true
#    orm:
#        default:
##            tree: true
#            translatable: true
#            sluggable: true

# Swiftmailer Configuration
swiftmailer:
    transport: "%mailer_transport%"
    host:      "%mailer_host%"
    username:  "%mailer_user%"
    password:  "%mailer_password%"
    spool:     { type: memory }

# Knp Paginator
knp_paginator:
    page_range: 5                      # default page range used in pagination control
    default_options:
        page_name: page                # page query parameter name
        sort_field_name: sort          # sort field query parameter name
        sort_direction_name: direction # sort direction query parameter name
        distinct: true                 # ensure distinct results, useful when ORM queries are using GROUP BY statements
        # filter_field_name: filterField  # filter field query parameter name
        # filter_value_name: filterValue  # filter value query parameter name
    template:
        pagination: AppBundle:Pagination:sliding.html.twig     # sliding pagination controls template
        sortable: KnpPaginatorBundle:Pagination:sortable_link.html.twig # sort link template
        filtration: KnpPaginatorBundle:Pagination:filtration.html.twig  # filters template

# Knp Doctrine Behaviors
knp_doctrine_behaviors:
    blameable:      true
    geocodable:     ~     # Here null is converted to false
    loggable:       ~
    timestampable:  true
    sluggable:      true
    soft_deletable: true
    # All others behaviors are disabled
    
# Liip
liip_imagine :
    # configure resolvers
    resolvers :
        # setup the default resolver
        default :
            # use the default web path
            web_path : ~
    # your filter sets are defined here
    filter_sets :
        # use the default cache configuration
        cache : ~
        # the name of the "filter set"
        logo_thumb :
            # adjust the image quality to 75%
            quality : 100
            # list of transformations to apply (the "filters")
            filters :
                # create a thumbnail: set size to 120x90 and use the "outbound" mode
                # to crop the image when the size ratio of the input differs
                thumbnail  : { size : [50, 50], mode : outbound }
        
        # the name of the "filter set"
        product_small :
            # adjust the image quality to 75%
            quality : 100
            # list of transformations to apply (the "filters")
            filters :
                # create a thumbnail: set size to 120x90 and use the "outbound" mode
                # to crop the image when the size ratio of the input differs
                thumbnail  : { size : [400, 400], mode : outbound }
        
        # the name of the "filter set"
        product_thumb :
            # adjust the image quality to 75%
            quality : 100
            # list of transformations to apply (the "filters")
            filters :
                # create a thumbnail: set size to 120x90 and use the "outbound" mode
                # to crop the image when the size ratio of the input differs
                thumbnail  : { size : [95, 60], mode : outbound }

        # the name of the "filter set"
        thumb :
            # adjust the image quality to 75%
            quality : 100
            # list of transformations to apply (the "filters")
            filters :
                # create a thumbnail: set size to 120x90 and use the "outbound" mode
                # to crop the image when the size ratio of the input differs
                thumbnail  : { size : [95, 60], mode : outbound }

# Admin Configuration
admin:
    website:
        title: 'Admin Puzzle' # Customize with your own admin name
        description: 'Lorem ipsum' # Customize with your own admin description
        email: 'johndoe@exemple.ci' # Customize with your own admin email
    time_format: "H:i" # customize time format
    date_format: "d-m-Y" # customize date format
    modules_available: 'admin'
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
                parent: ~
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
user:
    resource: "@UserBundle/Resources/config/routing.yml"
    prefix:   /

```
See all user routes by typing: `php bin/console debug:router | grep user`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
        modules_available: '..,user'
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
                user_roles: ['ROLE_ACCOUNT']
            user_list:
                label: 'user.account.sidebar'
                translation_domain: 'messages'
                path: 'admin_user_list'
                sub_paths: ['admin_user_create', 'admin_user_update', 'admin_user_show']
                parent: user
                user_roles: ['ROLE_ACCOUNT']
            user_group:
                label: 'user.group.sidebar'
                translation_domain: 'messages'
                path: 'admin_user_group_list'
                sub_paths: ['admin_user_group_create', 'admin_user_group_update', 'admin_user_group_show']
                parent: user
                user_roles: ['ROLE_ACCOUNT']

# Puzzle User configuration
user:
    registration:
        confirmation_link: true # Send confirmation url to enable account manually
        redirect_uri: '' # redirect uri after registration
        address: 'johndoe@exemple.ci' # registration address
```
### 
### **Step 4: Configure role hierarchy and Admin ACL**
Configure security by adding it in the `app/config/security.yml` file of your project:
```yaml
security:
    ...
    role_hierarchy:
        ...
        # User
        ROLE_ACCOUNT: ROLE_ADMIN
        ROLE_SUPER_ADMIN: [..,ROLE_ACCOUNT]
        ...
    access_control:
        ...
        # User
        - {path: ^%admin_prefix%user, host: "%admin_host%", roles: ROLE_ACCOUNT }

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
media:
    resource: "@MediaBundle/Resources/config/routing.yml"
    prefix:   /

```
See all media routes by typing: `php bin/console debug:router | grep media`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
    modules_available: 'media'
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
                user_roles: ['ROLE_MEDIA']
            media_file:
                label: 'media.file.sidebar'
                description: 'media.file.description'
                translation_domain: 'messages'
                path: 'admin_media_file_list'
                parent: media
                user_roles: ['ROLE_MEDIA']
            media_folder:
                label: 'media.folder.sidebar'
                description: 'media.folder.description'
                translation_domain: 'messages'
                path: 'admin_media_folder_list'
                sub_paths: ['admin_media_folder_create', 'admin_media_folder_update', 'admin_media_folder_show']
                parent: media
                user_roles: ['ROLE_MEDIA']
```
### 
### **Step 4: Define base directory**
Defined media base directory by adding it in the `app/config/parameters.yml` file of your project:
```yaml
...
media_base_dir: '%kernel.root_dir%/../web'
```

### **Step 5: Configure role hierarchy and Admin ACL**
Configure security by adding it in the `app/config/security.yml` file of your project:
```yaml
security:
    ...
    role_hierarchy:
        ...
        # Media
        ROLE_MEDIA: ROLE_ADMIN
        ROLE_SUPER_ADMIN: [..,ROLE_MEDIA]
        ...
    access_control:
        ...
        # Media
        - {path: ^%admin_prefix%media, host: "%admin_host%", roles: ROLE_MEDIA }

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
    modules_available: '..,blog'
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
                user_roles: ['ROLE_BLOG']
            blog_post:
                label: 'blog.navigation.post'
                description: 'blog.post.description'
                translation_domain: 'messages'
                path: 'admin_blog_post_list'
                sub_paths: ['admin_blog_post_create', 'admin_blog_post_update', 'admin_blog_post_show']
                parent: blog
                user_roles: ['ROLE_BLOG']
            blog_category:
                label: 'blog.navigation.category'
                description: 'blog.category.description'
                translation_domain: 'messages'
                path: 'admin_blog_category_list'
                sub_paths: ['admin_blog_category_create', 'admin_blog_category_update', 'admin_blog_category_show']
                parent: blog
                user_roles: ['ROLE_BLOG']
                
# App Configuration
app:
    ...
    navigation:
        nodes:
            ...
            blog_post:
                label: 'app.blog.title'
                translation_domain: 'app'
                path: app_blog_post_list
                sub_paths: [app_blog_post_show]
                parent: ~
```

### **Step 4: Configure role hierarchy and Admin ACL**
Configure security by adding it in the `app/config/security.yml` file of your project:
```yaml
security:
    ...
    role_hierarchy:
        ...
        # Blog
        ROLE_BLOG: ROLE_ADMIN
        ROLE_SUPER_ADMIN: [..,ROLE_BLOG]
        ...
    access_control:
        ...
        # Blog
        - {path: ^%admin_prefix%blog, host: "%admin_host%", roles: ROLE_BLOG }

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
calendar:
    resource: "@CalendarBundle/Resources/config/routing.yml"
    prefix:   /

```
See all calendar routes by typing: `php bin/console debug:router | grep calendar`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
    modules_available: '..,calendar'
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
                user_roles: ['ROLE_CALENDAR']
            calendar_agenda:
                label: 'calendar.agenda.sidebar'
                description: 'calendar.agenda.description'
                translation_domain: 'messages'
                path: 'admin_calendar_agenda_list'
                parent: calendar
                user_roles: ['ROLE_CALENDAR']
            calendar_moment:
                label: 'calendar.moment.sidebar'
                description: 'calendar.moment.description'
                translation_domain: 'messages'
                path: 'admin_calendar_moment_list'
                sub_paths: ['admin_calendar_moment_create', 'admin_calendar_moment_update', 'admin_calendar_moment_show']
                parent: calendar
                user_roles: ['ROLE_CALENDAR']
```

### **Step 4: Requirements**
For this bundle to work, you have to install [supervisord](http://supervisord.org/installing.html)

### **Step 5: Configure role hierarchy and Admin ACL**
Configure security by adding it in the `app/config/security.yml` file of your project:
```yaml
security:
    ...
    role_hierarchy:
        ...
        # Calendar
        ROLE_CALENDAR: ROLE_ADMIN
        ROLE_SUPER_ADMIN: [..,ROLE_CALENDAR]
        ...
    access_control:
        ...
        # Calendar
        - {path: ^%admin_prefix%calendar, host: "%admin_host%", roles: ROLE_CALENDAR }

```


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
contact:
    resource: "@ContactBundle/Resources/config/routing.yml"
    prefix:   /

```
See all contact routes by typing: `php bin/console debug:router | grep contact`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
    modules_available: '..,contact'
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
                user_roles: ['ROLE_CONTACT']
            contact_list:
                label: 'contact.sidebar'
                translation_domain: 'messages'
                path: 'admin_contact_list'
                sub_paths: ['admin_contact_create', 'admin_contact_update']
                parent: contact
                user_roles: ['ROLE_CONTACT']
            contact_group:
                label: 'contact.group.sidebar'
                translation_domain: 'messages'
                path: 'admin_contact_group_list'
                sub_paths: ['admin_contact_group_create', 'admin_contact_group_update']
                parent: contact
                user_roles: ['ROLE_CONTACT']
            contact_request:
                label: 'contact.request.sidebar'
                translation_domain: 'messages'
                path: 'admin_contact_request_list'
                sub_paths: ['admin_contact_request_show', 'admin_contact_request_update']
                parent: contact
                user_roles: ['ROLE_CONTACT']
```

### **Step 4: Configure role hierarchy and Admin ACL**
Configure security by adding it in the `app/config/security.yml` file of your project:
```yaml
security:
    ...
    role_hierarchy:
        ...
        # Contact
        ROLE_CONTACT: ROLE_ADMIN
        ROLE_SUPER_ADMIN: [..,ROLE_CONTACT]
        ...
    access_control:
        ...
        # Contact
        - {path: ^%admin_prefix%calendar, host: "%admin_host%", roles: ROLE_CONTACT }

```

---

## **Expertise Bundle**

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

            new Puzzle\ExpertiseBundle\ExpertiseBundle(),
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
expertise:
    resource: "@ExpertiseBundle/Resources/config/routing.yml"
    prefix:   /

```
See all contact expertise by typing: `php bin/console debug:router | grep expertise`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
    modules_available: '..,expertise'
    navigation:
        nodes:
            ...
            # Expertise
            expertise:
                label: 'expertise.title'
                description: 'expertise.description'
                translation_domain: 'messages'
                attr:
                    class: 'fa fa-suitcase'
                parent: ~
                user_roles: ['ROLE_EXPERTISE']
            expertise_service:
                label: 'expertise.service.sidebar'
                translation_domain: 'messages'
                path: 'admin_expertise_service_list'
                sub_paths: ['admin_expertise_service_create', 'admin_expertise_service_update']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']
            expertise_feature:
                label: 'expertise.feature.sidebar'
                translation_domain: 'messages'
                path: 'admin_expertise_feature_list'
                sub_paths: ['admin_expertise_feature_create', 'admin_expertise_feature_update']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']
            expertise_project:
                label: 'expertise.project.sidebar'
                translation_domain: 'messages'
                path: 'admin_expertise_project_list'
                sub_paths: ['admin_expertise_project_create', 'admin_expertise_project_update', 'admin_expertise_project_update_gallery']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']
            expertise_staff:
                label: 'expertise.staff.sidebar'
                translation_domain: 'messages'
                path: 'admin_expertise_staff_list'
                sub_paths: ['admin_expertise_staff_create', 'admin_expertise_staff_update']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']
            expertise_pricing:
                label: 'expertise.pricing.sidebar'
                translation_domain: 'messages'
                path: 'admin_expertise_pricing_list'
                sub_paths: ['admin_expertise_pricing_create', 'admin_expertise_pricing_update']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']
            expertise_partner:
                label: 'expertise.partner.sidebar'
                translation_domain: 'messages'
                path: 'admin_expertise_partner_list'
                sub_paths: ['admin_expertise_partner_create', 'admin_expertise_partner_update']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']
            expertise_testimonial:
                label: 'expertise.testimonial.sidebar'
                translation_domain: 'messages'
                path: 'admin_expertise_testimonial_list'
                sub_paths: ['admin_expertise_testimonial_create', 'admin_expertise_testimonial_update']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']
            expertise_faq:
                label: 'expertise.faq.sidebar'
                translation_domain: 'messages'
                path: 'admin_expertise_faq_list'
                sub_paths: ['admin_expertise_faq_create', 'admin_expertise_faq_update']
                parent: expertise
                user_roles: ['ROLE_EXPERTISE']
```

### **Step 4: Configure role hierarchy and Admin ACL**
Configure security by adding it in the `app/config/security.yml` file of your project:
```yaml
security:
    ...
    role_hierarchy:
        ...
        # Expertise
        ROLE_EXPERTISE: ROLE_ADMIN
        ROLE_SUPER_ADMIN: [..,ROLE_EXPERTISE]
        ...
    access_control:
        ...
        # Expertise
        - {path: ^%admin_prefix%expertise, host: "%admin_host%", roles: ROLE_EXPERTISE }

```

---

## **Newsletter Bundle**

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

            new Puzzle\NewsletterBundle\NewsletterBundle(),
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
newsletter:
    resource: "@NewsletterBundle/Resources/config/routing.yml"
    prefix:   /

```
See all contact newsletter by typing: `php bin/console debug:router | grep newsletter`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
    modules_available: '..,newsletter'
    navigation:
        nodes:
            ...
            # Newsletter
            newsletter:
                label: 'newsletter.title'
                description: 'newsletter.description'
                translation_domain: 'messages'
                attr:
                    class: 'fa fa-envelope-o'
                parent: ~
                user_roles: ['ROLE_NEWSLETTER']
            newsletter_subscriber:
                label: 'newsletter.subscriber.sidebar'
                translation_domain: 'messages'
                path: 'admin_newsletter_subscriber_list'
                sub_paths: ['admin_newsletter_subscriber_create', 'admin_newsletter_subscriber_update']
                parent: newsletter
                user_roles: ['ROLE_NEWSLETTER']
            newsletter_group:
                label: 'newsletter.group.sidebar'
                translation_domain: 'messages'
                path: 'admin_newsletter_group_list'
                sub_paths: ['admin_newsletter_group_create', 'admin_newsletter_group_update']
                parent: newsletter
                user_roles: ['ROLE_NEWSLETTER']
            newsletter_template:
                label: 'newsletter.template.sidebar'
                translation_domain: 'messages'
                path: 'admin_newsletter_template_list'
                sub_paths: ['admin_newsletter_template_create', 'admin_newsletter_template_update']
                parent: newsletter
                user_roles: ['ROLE_NEWSLETTER']
```

### **Step 4: Configure role hierarchy and Admin ACL**
Configure security by adding it in the `app/config/security.yml` file of your project:
```yaml
security:
    ...
    role_hierarchy:
        ...
        # Newsletter
        ROLE_NEWSLETTER: ROLE_ADMIN
        ROLE_SUPER_ADMIN: [..,ROLE_NEWSLETTER]
        ...
    access_control:
        ...
        # Newsletter
        - {path: ^%admin_prefix%newsletter, host: "%admin_host%", roles: ROLE_NEWSLETTER }

```

---

## **Static Bundle**

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

            new Puzzle\StaticBundle\StaticBundle(),
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
static:
    resource: "@StaticBundle/Resources/config/routing.yml"
    prefix:   /

```
See all contact static by typing: `php bin/console debug:router | grep static`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
    modules_available: '..,static'
    navigation:
        nodes:
            ...
            # Static
            static:
                label: 'static.title'
                description: 'static.description'
                translation_domain: 'messages'
                sub_paths: ['admin_static_page_create', 'admin_static_page_update']
                attr:
                    class: 'fa fa-file-text'
                parent: ~
                user_roles: ['ROLE_STATIC']
            static_page:
                label: 'static.page.sidebar'
                description: 'static.page.description'
                translation_domain: 'messages'
                path: 'admin_static_page_list'
                sub_paths: ['admin_static_page_create', 'admin_static_page_update']
                parent: static
                user_roles: ['ROLE_STATIC']
            static_template:
                label: 'static.template.sidebar'
                description: 'static.template.description'
                translation_domain: 'messages'
                path: 'admin_static_template_list'
                sub_paths: ['admin_static_template_create', 'admin_static_template_update']
                parent: static
                user_roles: ['ROLE_STATIC']
```

### **Step 4: Configure role hierarchy and Admin ACL**
Configure security by adding it in the `app/config/security.yml` file of your project:
```yaml
security:
    ...
    role_hierarchy:
        ...
        # Static
        ROLE_STATIC: ROLE_ADMIN
        ROLE_SUPER_ADMIN: [..,ROLE_STATIC]
        ...
    access_control:
        ...
        # Static
        - {path: ^%admin_prefix%static, host: "%admin_host%", roles: ROLE_STATIC }

```

---

## **Advert Bundle**

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

            new Puzzle\AdvertBundle\AdvertBundle(),
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
advert:
    resource: "@AdvertBundle/Resources/config/routing.yml"
    prefix:   /

```
See all advert routes by typing: `php bin/console debug:router | grep advert`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
    modules_available: '..,advert'
    navigation:
        nodes:
            ...
            # Advert
            advert:
                label: 'advert.title'
                description: 'advert.description'
                translation_domain: 'messages'
                attr:
                    class: 'fa fa-newspaper-o'
                parent: ~
                user_roles: ['ROLE_ADVERT']
            advert_post:
                label: 'advert.navigation.post'
                description: 'advert.post.description'
                translation_domain: 'messages'
                path: 'admin_advert_post_list'
                sub_paths: ['admin_advert_post_create', 'admin_advert_post_update', 'admin_advert_post_show']
                parent: advert
                user_roles: ['ROLE_ADVERT']
            advert_category:
                label: 'advert.navigation.category'
                description: 'advert.category.description'
                translation_domain: 'messages'
                path: 'admin_advert_category_list'
                sub_paths: ['admin_advert_category_create', 'admin_advert_category_update', 'admin_advert_category_show']
                parent: advert
                user_roles: ['ROLE_ADVERT']
                
# App Configuration
app:
    ...
    navigation:
        nodes:
            ...
            advert_post:
                label: 'app.advert.title'
                translation_domain: 'app'
                path: app_advert_post_list
                sub_paths: [app_advert_post_show]
                parent: ~
```

### **Step 4: Configure role hierarchy and Admin ACL**
Configure security by adding it in the `app/config/security.yml` file of your project:
```yaml
security:
    ...
    role_hierarchy:
        ...
        # Advert
        ROLE_ADVERT: ROLE_ADMIN
        ROLE_SUPER_ADMIN: [..,ROLE_ADVERT]
        ...
    access_control:
        ...
        # Advert
        - {path: ^%admin_prefix%advert, host: "%admin_host%", roles: ROLE_ADVERT }

```


---

## **Charity Bundle**

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

            new Puzzle\CharityBundle\CharityBundle(),
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
advert:
    resource: "@CharityBundle/Resources/config/routing.yml"
    prefix:   /

```
See all charity routes by typing: `php bin/console debug:router | grep charity`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
    modules_available: '..,charity'
    navigation:
        nodes:
            ...
            # Charity
            charity:
                label: 'charity.title'
                description: 'charity.description'
                translation_domain: 'messages'
                attr:
                    class: 'fa fa-heart'
                parent: ~
                user_roles: ['ROLE_CHARITY']
            charity_cause:
                label: 'charity.cause.sidebar'
                description: 'charity.cause.sidebar'
                translation_domain: 'messages'
                path: 'admin_charity_cause_list'
                sub_paths: ['admin_charity_cause_create', 'admin_charity_cause_update', 'admin_charity_cause_show', 'admin_charity_donation_list', 'admin_charity_donation_create', 'admin_charity_donation_update', 'admin_charity_donation_show', 'admin_charity_donation_update_lines']
                parent: charity
                user_roles: ['ROLE_CHARITY']
            charity_category:
                label: 'charity.category.sidebar'
                description: 'charity.category.sidebar'
                translation_domain: 'messages'
                path: 'admin_charity_category_list'
                sub_paths: ['admin_charity_category_create', 'admin_charity_category_update', 'admin_charity_category_show']
                parent: charity
                user_roles: ['ROLE_CHARITY']
            charity_member:
                label: 'charity.member.sidebar'
                description: 'charity.member.sidebar'
                translation_domain: 'messages'
                path: 'admin_charity_member_list'
                sub_paths: ['admin_charity_member_create', 'admin_charity_member_update', 'admin_charity_member_show']
                parent: charity
                user_roles: ['ROLE_CHARITY']
            charity_group:
                label: 'charity.group.sidebar'
                description: 'charity.group.sidebar'
                translation_domain: 'messages'
                path: 'admin_charity_group_list'
                sub_paths: ['admin_charity_group_create', 'admin_charity_group_update', 'admin_charity_group_show']
                parent: charity
                user_roles: ['ROLE_CHARITY']
                
# App Configuration
app:
    ...
    navigation:
        nodes:
            ...
            charity_cause:
                label: 'app.charity.title'
                translation_domain: 'app'
                path: app_charity_cause_list
                sub_paths: [app_charity_cause_show]
                parent: ~
```

### **Step 4: Configure role hierarchy and Admin ACL**
Configure security by adding it in the `app/config/security.yml` file of your project:
```yaml
security:
    ...
    role_hierarchy:
        ...
        # Charity
        ROLE_CHARITY: ROLE_ADMIN
        ROLE_SUPER_ADMIN: [..,ROLE_CHARITY]
        ...
    access_control:
        ...
        # Charity
        - {path: ^%admin_prefix%charity, host: "%admin_host%", roles: ROLE_CHARITY }

```


---

## **Curriculum Bundle**

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

            new Puzzle\CurriculumBundle\CurriculumBundle(),
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
    resource: "@CurriculumBundle/Resources/config/routing.yml"
    prefix:   /

```
See all curriculum routes by typing: `php bin/console debug:router | grep curriculum`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
    modules_available: '..,curriculum'
    navigation:
        nodes:
            ...
            # Curriculum
            curriculum:
                label: 'curriculum.title'
                description: 'curriculum.description'
                translation_domain: 'messages'
                attr:
                    class: 'fa fa-user-secret'
                parent: ~
                user_roles: ['ROLE_CURRICULUM']
                path: 'admin_curriculum_applicant_list'
                sub_paths: ['admin_curriculum_applicant_create', 'admin_curriculum_applicant_update', 'admin_curriculum_applicant_show']
```

### **Step 4: Configure role hierarchy and Admin ACL**
Configure security by adding it in the `app/config/security.yml` file of your project:
```yaml
security:
    ...
    role_hierarchy:
        ...
        # Curriculum
        ROLE_CURRICULUM: ROLE_ADMIN
        ROLE_SUPER_ADMIN: [..,ROLE_CURRICULUM]
        ...
    access_control:
        ...
        # Learning
        - {path: ^%admin_prefix%curriculum, host: "%admin_host%", roles: ROLE_CURRICULUM }

```


---

## **Learning Bundle**

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

            new Puzzle\LearningBundle\LearningBundle(),
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
    resource: "@LearningBundle/Resources/config/routing.yml"
    prefix:   /

```
See all learning routes by typing: `php bin/console debug:router | grep learning`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
    modules_available: '..,learning'
    navigation:
        nodes:
            ...
            # Learning
            learning:
                label: 'learning.title'
                description: 'learning.description'
                translation_domain: 'messages'
                attr:
                    class: 'fa fa-microphone'
                parent: ~
                user_roles: ['ROLE_LEARNING']
            learning_post:
                label: 'learning.post.sidebar'
                description: 'learning.post.description'
                translation_domain: 'messages'
                path: 'admin_learning_post_list'
                sub_paths: ['admin_learning_post_create', 'admin_learning_post_update', 'admin_learning_post_show']
                parent: learning
                user_roles: ['ROLE_LEARNING']
            learning_category:
                label: 'learning.category.sidebar'
                description: 'learning.category.description'
                translation_domain: 'messages'
                path: 'admin_learning_category_list'
                sub_paths: ['admin_learning_category_create', 'admin_learning_category_update', 'admin_learning_category_show']
                parent: learning
                user_roles: ['ROLE_LEARNING']
                
# App Configuration
app:
    ...
    navigation:
        nodes:
            ...
            learning_post:
                label: 'app.learning.title'
                translation_domain: 'app'
                path: app_learning_post_list
                sub_paths: [app_learning_post_show]
                parent: ~
```

### **Step 4: Configure role hierarchy and Admin ACL**
Configure security by adding it in the `app/config/security.yml` file of your project:
```yaml
security:
    ...
    role_hierarchy:
        ...
        # Learning
        ROLE_LEARNING: ROLE_ADMIN
        ROLE_SUPER_ADMIN: [..,ROLE_LEARNING]
        ...
    access_control:
        ...
        # Learning
        - {path: ^%admin_prefix%learning, host: "%admin_host%", roles: ROLE_LEARNING }

```
