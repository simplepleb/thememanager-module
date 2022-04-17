<?php


if (!function_exists('theme_has_menus')) {
    /**
     * Helper to check if theme has menu.
     *
     * @param $id
     * @return mixed
     */
    function theme_has_menus($id)
    {
        if( \Module::has('Thememanager')) {
            $site_theme = \Modules\Thememanager\Entities\SiteTheme::where('id', $id)->first();
            $file = public_path('themes/'.$site_theme->slug.'/custom_fields.json');
            if( file_exists($file )) {
                $json_menu = json_decode(file_get_contents($file));
                if (isset($json_menu->menus)) {
                    return true;
                }
            }
        }

        return false;
    }
}
