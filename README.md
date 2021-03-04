# ThemeManager Module
Theme Management (Front End) For Laravel CMS

## After Install 
You have to publish the custom blade components. The following command will copy files to the 
``` resources/views/components/buttons ``` folder.

``` php artisan vendor:publish --tag=theme-manager ```

## Before Updating Package 
Before you run ``` composer update ``` and if you have edited any of the included themes,
* Duplicate the folder for the edited theme
* Update the theme.json file (theme name and slug) in the new folder.

This is done to avoid the update overwriting your changes with any updates made to the module package.
