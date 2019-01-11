Puzzle Blog Bundle
==================

Bundle based on symfony for managing static pages.

Installation

Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

`composer require webundle/puzzle-blog-bundle`

Step 2: Enable the Bundle

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
Step 3: Register the Routes

Load the bundle's routing definition in the application (usually in the `app/config/routing.yml` file):

# app/config/routing.yml
```puzzle_blog:
    resource: "@PuzzleBlogBundle/Resources/config/routing.yml"
```

Step 4: Enable management via Admin modules interface

Then, enable management bundle via admin modules interface by adding it to the list of registered bundles in the `app/config/config.yml` file of your project under:

```puzzle:
    modules_available: '....,blog'
```

Step 5: Configure admin menu

Then, configure admin menu in the `app/config/config.yml` file of your project under:

```admin:
    ...
    navigation:
        ...
        blog:
            label: 'blog.navigation.title'
            translation_domain: 'messages'
            attr:
                class: 'fa fa-pencil'
            parent: ~
            user_roles: ['ROLE_BLOG_MANAGE', 'ROLE_ADMIN']
            tooltip: 'blog.navigation.description'
        blog_post:
            label: 'blog.navigation.sub_menu.posts'
            translation_domain: 'messages'
            path: 'admin_blog_post_list'
            sub_paths: ['admin_blog_post_create', 'admin_blog_post_update']
            parent: blog
            user_roles: ['ROLE_BLOG_MANAGE', 'ROLE_ADMIN']
            tooltip: ''
        blog_category:
            label: 'blog.navigation.sub_menu.categories'
            translation_domain: 'messages'
            path: 'admin_blog_category_list'
            sub_paths: ['admin_blog_category_create', 'admin_blog_category_update', 'admin_blog_category_show']
            parent: blog
            user_roles: ['ROLE_BLOG_MANAGE', 'ROLE_ADMIN']
            tooltip: ''
```