# ThemeManager Module

Theme Management (Front End) For Laravel CMS.

## Dashboard

Using the theme manager dashboard you can click to change the theme of the site. Themes are displayed with a screenshot, description and related information. By clicking 'Activate' your site (front-end) theme will be updated to use the selected theme.

If you choose to disable the theme, by clicking the 'disable' button on the dashboard, your site will only display the default Laravel login page if it exists.

## Install

``` composer require simplepleb/thememanager-module ```

## After Install 

Publish the custom blade components. The following command will copy files to the folder. ``` resources/views/components/buttons ``` 

``` php artisan vendor:publish --tag=theme-manager ```

## Theme Views

Theme folders have json files that make it easy to use the correct view from your controller method. Take a look at ``` pages.json ``` in any of the theme folders for a better understanding.

## Simple Previews

It is simple to preview any theme view page. Open the ``` /thememanager/preview/{VIEW_PAGE}/{THEME_NAME} ``` in your browser. The ``` VIEW_PAGE ``` value is the name of the blade template file you want to preview. The (optional) ``` THEME_NAME ``` allows you to preview any theme with ease. If ``` THEME_NAME ``` is not specified, the preview will use the default theme.

## Screenshots

Dashboard

![Screen Shot 2021-03-03 at 8 59 17 PM](https://user-images.githubusercontent.com/79759974/109899123-5aafa880-7c63-11eb-8da9-67bc5d538e70.png)


