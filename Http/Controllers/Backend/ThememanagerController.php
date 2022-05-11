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

use App\Models\CustomField;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Auth;
use Carbon\Carbon;
use Flash;
use Log;
use Modules\Menumaker\Entities\MenuMaker;
use Modules\Menumaker\Entities\MenuMakerItem;
use Modules\Thememanager\Entities\SiteTheme;
use Theme;
use Menu;

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
            $themes = $module_model::whereNotNull('settings')->get();
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

    public function activate_theme(Request $request)
    {
        $id = $request->get('theme_id');

        SiteTheme::where('id', '<>', $id)->update(['active' => 0]);
        $theme = SiteTheme::where('id',$id)->first();
        $theme->active = 1;
        $theme->save();

        if ( \Module::has('Menumaker')) {

            $file = resource_path('themes/'.$theme->slug.'/custom_fields.json');
            if( file_exists($file )) {
                $json_menu = json_decode(file_get_contents($file));
                if( isset($json_menu->menus) ){
                    foreach($json_menu->menus as $row){

                        $mnu = MenuMaker::where('machine_name',$row->menu_name )->first();
                        if( $mnu ) continue;
                        $mnu = new MenuMaker();
                        $mnu->machine_name = $row->menu_name;
                        $mnu->menu_class = $row->menu_class;
                        $mnu->display_name = label_case($row->menu_name);
                        $mnu->lang = 'en';
                        $mnu->save();

                        foreach( $row->links as $link){
                            $itm = new MenuMakerItem();
                            $itm->menu_id = $mnu->id;
                            $itm->unique_name = slug_format($link->title.rand(1,900)) ;
                            $itm->parameters = '{}';
                            $itm->label = $link->title;
                            $itm->menu_text = $link->title;
                            $itm->link = $link->url;
                            $itm->class = $link->link_class;
                            $itm->save();
                        }


                        \Menu::make($row->menu_name, function ($menu) use ($row) {
                            foreach($row->links as $item  ){
                                // dd( $item );
                                $menu->add(__($item->title), [
                                    'url' => $item->url,
                                    'class' => $item->li_class,
                                ])->data([
                                    'order' => $item->order,
                                    'activematches' => $item->url,
                                ])->link->attr([
                                    'class' => $item->link_class,
                                ]);



                            }


                        });
                        // $menu_name = $row->menu_name;

                        // dd( $row );
                    }

                }

            }
        }


        $success = true;
        $message = __('Theme Activated');

        return response()->json([
            'success' => $success,
            'message' => $message,
        ]);

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

    public static function import_fields($slug){

        $custom_fields = CustomField::where('module', 'thememanager-'.$slug)->get();
        if( count($custom_fields) > 0 || !file_exists(resource_path('themes/'.$slug.'/custom_fields.json')))
            return; // already imported

        $fields_file = resource_path('themes/'.$slug.'/custom_fields.json');
// dd( $slug,file_get_contents($fields_file) );
        // dd( $fields_file->fields );
        if( file_exists($fields_file)) {
            $fields_json = json_decode( file_get_contents($fields_file) );
            if( !empty($fields_json))
            foreach( $fields_json->fields as $row ){

                $field = new CustomField();
                $field->field_name = $row->field_name;
                $field->module = 'thememanager-'.$slug;
                $field->field_type = $row->field_type;
                $field->field_options = $row->field_options;
                $field->field_help = $row->field_help;
                $field->field_value = $row->field_value;
                $field->save();
            }

            // dd( $fields_json );
        }


    }

    /**
     * Show the form for editing the specified resource.
     * @param $name
     * @return Renderable
     */
    public function edit($name){

        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'Settings';

        $$module_name_singular = SiteTheme::where('slug', $name)->first();
        self::import_fields($name);
        $custom_fields = array();
        $custom_fields = CustomField::where('module','thememanager-'.$name)->get();

        return view(
            "thememanager::backend.settings",
            compact( 'module_title', 'module_name', 'module_icon', 'module_name_singular', 'module_action', "$module_name_singular",'custom_fields')
        );
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id){

        $module_name = $this->module_name;

        $theme = SiteTheme::where('id',$id)->first();

        $theme->custom_css      = $request->get('custom_css');
        $theme->custom_script   = $request->get('custom_script');
        $theme->save();

        if($request->get('has_custom') == 1){ // save custom fields

            $db_fields = CustomField::where('module','thememanager-'.$theme->slug)->get();
            foreach($db_fields as $field){
                if( $request->get($field->field_name) ) {
                    $c_field = CustomField::where('field_name',$field->field_name)->first();
                    if( $c_field ) {
                        $c_field->field_value = $request->get($field->field_name);
                        $c_field->save();
                    }
                }
            }

        }

        // Flash::success("<i class='fas fa-check'></i> ".$theme->name." Theme Updated")->important();

        Log::info(" | '".$theme->name.'(ID:'.$theme->id.") ' Updated by User:".Auth::user()->name.'(ID:'.Auth::user()->id.')');

        return redirect()->back()->with('success', __('Theme Settings Saved.'));

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

        // Flash::success("<i class='fas fa-check'></i> Themes Disabled")->important();

        // Log::info(label_case($module_title.' '.$module_action)." | '".$$module_name_singular->name.'(ID:'.$$module_name_singular->id.") ' by User:".Auth::user()->name.'(ID:'.Auth::user()->id.')');

        return redirect()->route('backend.thememanager');
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

    public function refresh($console = false)
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'Store';

        $dir = resource_path('themes/').'*';
        $path = resource_path().'/themes/';
        $theme_setings = array();
        $page_styles = array();

        $current = 0;
        $active = SiteTheme::where('active', 1)->first();
        if( $active && $active->id > 0 )
            $current = $active->id;

        // Open a known directory, and proceed to read its contents
        foreach(glob($dir) as $file)
        {
            $theme_setings = array();
            $page_styles = array();
            // dd(filetype($file) );
            if( filetype($file) == 'dir'){
                $name = str_replace($path,'',$file);
                // Theme Settings
                if( file_exists($path.$name.'/theme.json')) {
                    $settings = file_get_contents($path.$name.'/theme.json');
                    $settings = preg_replace( "/\r|\n/", "", $settings );
                    $settings = '['.$settings.']';
                    $vals = json_decode($settings);

                    if( is_array($vals)) {
                        $theme_setings = $vals[0];
                    }
                    else
                        $theme_setings = null;

                    // dd( $theme_setings, 'WHERE' );
                    unset($vals);
                }

                if( file_exists($path.$name.'/pages.json')) {
                    // Page Styles
                    $pages = file_get_contents($path.$name.'/pages.json');
                    $pages = preg_replace( "/\r|\n/", "", $pages );
                    $pages = '['.$pages.']';
                    $vals = json_decode($pages);
                    if( is_array($vals)) {
                        $page_styles = $vals[0];
                    }
                    else
                        $page_styles = null;

                }

                if( $theme_setings || $page_styles ){

                    SiteTheme::updateOrCreate(
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


            }

        }

        if( $current > 0 ) {
            SiteTheme::where('id', $current)->update(['active' => 1]);
        }

        // Flash::success("<i class='fas fa-check'></i> Themes Refreshed")->important();

        if( $console === false ){
        Log::info("Themes Refreshed by User:".Auth::user()->name." (ID:".Auth::user()->id.")");
        return redirect()->back()->with('success', "Theme Files Were Refreshed");
        }
        else {
            return true;
        }




    }

    public static function themeFields($slug, $key)
    {
        $base = [
            'home_title', 'site_footer', 'bottom_copyright',
        ];

        $b = collect($base)->map(function($item) use ($key) {
            if(is_null($key)) {
                return $item;
            }
            return $key.'.'.$item;
        });

        $cf = custom_fields_names('theme-'.$slug);
        $c = collect($cf)->map(function($item) use ($key) {
            if(is_null($key)) {
                return $item;
            }
            return $key.'.'.$item;
        });

        $f = $b->merge($c);
        return $f->toArray();

    }





}
