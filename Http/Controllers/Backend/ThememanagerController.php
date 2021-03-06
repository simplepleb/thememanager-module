<?php

/**
 * Putting this here to help remind you where this came from.
 *
 * I'll get back to improving this and adding more as time permits
 * if you need some help feel free to drop me a line.
 *
 * * Twenty-Years Experience
 * * PHP, JavaScript, Laravel, MySQL, Java, Python and so many more!
 *
 *
 * @author  Simple-Pleb <plebeian.tribune@protonmail.com>
 * @website https://www.simple-pleb.com
 * @source https://github.com/simplepleb/thememanager-module
 *
 * @license Free to do as you please
 *
 * @since 1.0
 *
 */

namespace Modules\Thememanager\Http\Controllers\Backend;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Auth;
use Carbon\Carbon;
use Flash;
use Log;
use Modules\Thememanager\Entities\SiteTheme;
use Theme;

class ThememanagerController extends Controller
{


    /**
     * @var string
     */
    private $module_title;
    /**
     * @var string
     */
    private $module_name;
    /**
     * @var string
     */
    private $module_path;
    /**
     * @var string
     */
    private $module_icon;
    /**
     * @var string
     */
    private $module_model;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Thememanager';

        // module name
        $this->module_name = 'thememanager';

        // directory path of the module
        $this->module_path = 'thememanager';

        // module icon
        $this->module_icon = 'fas fa-file-alt';

        // module model name, path
        $this->module_model = "Modules\Thememanager\Entities\SiteTheme";
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'List';

        $$module_name = $module_model::paginate();
        $themes = $module_model::get();
        // If no themes in the db lets refresh from file system
        if( $themes->isEmpty() ) {
            self::refresh();
            $themes = $module_model::get();
        }


        Log::info(label_case($module_title.' '.$module_action).' | User:'.Auth::user()->name.'(ID:'.Auth::user()->id.')');


        return view(
            "thememanager::backend.index",
            compact('module_title', 'module_name', "$module_name", 'module_icon', 'module_name_singular', 'module_action','themes')
        );

    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('thememanager::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('thememanager::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('thememanager::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function disable()
    {
        SiteTheme::where('active',1)->update(['active'=>0]);

        Flash::success("<i class='fas fa-check'></i> Themes Disabled")->important();

        // Log::info(label_case($module_title.' '.$module_action)." | '".$$module_name_singular->name.'(ID:'.$$module_name_singular->id.") ' by User:".Auth::user()->name.'(ID:'.Auth::user()->id.')');

        return redirect("admin/thememanager");
    }

    /**
     *
     * @param string $vname the name of the view file to be previewed
     * @param string $name the theme name to preview
     * @return mixed
     */
    public function preview($vname, $name= 'default')
    {
         // oreo, huckbee, digincy
        if( $vname == 'login' || $vname == 'register' ){
            Theme::uses($name)->layout('auth');
        }
        else {
            Theme::uses($name);
        }
        Theme::set('title','Home');
        $data['info'] = 'Hello World';

        return Theme::view($vname, $data);
    }

    public function refresh()
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'Store';

        $dir = public_path('themes/').'*';
        $path = public_path().'/themes/';

        // Open a known directory, and proceed to read its contents
        foreach(glob($dir) as $file)
        {
            // dd(filetype($file) );
            if( filetype($file) == 'dir'){
                $name = str_replace($path,'',$file);
                // Theme Settings
                $settings = file_get_contents($path.$name.'/theme.json');
                $settings = preg_replace( "/\r|\n/", "", $settings );
                $settings = '['.$settings.']';
                $vals = json_decode($settings);

                $theme_setings = $vals[0];
                unset($vals);

                // Page Styles
                $pages = file_get_contents($path.$name.'/pages.json');
                $pages = preg_replace( "/\r|\n/", "", $pages );
                $pages = '['.$pages.']';
                $vals = json_decode($pages);
                $page_styles = $vals[0];

                // dd( $vals->slug );
                $theme = SiteTheme::where('slug', $theme_setings->slug)->first();
                if( !$theme ) {
                    $theme  = SiteTheme::updateOrCreate(
                        [
                            'slug' => $theme_setings->slug
                        ],
                        [
                            'name' => $theme_setings->name,
                            'settings' => json_encode($theme_setings),
                            'page_styles' => json_encode($page_styles),
                            'active' => 0
                        ]
                    );
                }

                // dd( $theme );
            }

            // dd( $name );
            //echo "filename: $file : filetype: " . filetype($file) . "<br />";

        }

        Flash::success("<i class='fas fa-check'></i> Themes Refreshed")->important();

        // Log::info(label_case($module_title.' '.$module_action)." | '".$$module_name_singular->name.'(ID:'.$$module_name_singular->id.") ' by User:".Auth::user()->name.'(ID:'.Auth::user()->id.')');

        return redirect("admin/$module_name");
    }

    public function settings($name){

        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'Settings';

        /*$is = module_path('Thememanager', 'Resources/lang');
        dd( $is );*/
        $lan = trans('thememanager::custom_css');
        dd( $lan );

        $$module_name_singular = SiteTheme::where('slug', $name)->first();
        //dd( $settings );

        Log::info(label_case($module_title.' '.$module_action).' | User:'.Auth::user()->name.'(ID:'.Auth::user()->id.')');

        return view(
            "thememanager::backend.settings",
            compact( 'module_title', 'module_name', 'module_icon', 'module_name_singular', 'module_action', "$module_name_singular")
        );
    }


}
