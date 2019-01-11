Puzzle Calendar Bundle
==================

Bundle based on symfony for managing static pages.

Installation

Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

`composer require webundle/puzzle-static-bundle`

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

            new Puzzle\CalendarBundle\PuzzleCalendarBundle(),
        );

        // ...
    }

    // ...
}
`
Step 3: Register the Routes

Load the bundle's routing definition in the application (usually in the `app/config/routing.yml` file):

# app/config/routing.yml
`puzzle_static:
    resource: "@PuzzleUserBundle/Resources/config/routing.yml"`

Step 4: Enable management via Admin modules interface

Then, enable management bundle via admin modules interface by adding it to the list of registered bundles in the `app/config/config.yml` file of your project under:

`puzzle:
    modules_available: '....,static'
`
Step 5: Add translations
