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

namespace Modules\Thememanager\Http\Controllers\Frontend;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Theme;

class ThememanagerController extends Controller
{


    public function __construct()
    {
        // Page Title
        $this->module_title = 'Thememanager';

        // module name
        $this->module_name = 'thememanager';

        // directory path of the module
        $this->module_path = 'themes';

        // module icon
        $this->module_icon = 'fas fa-file-alt';

        // module model name, path
        $this->module_model = "Modules\Thememanager\Entities\Theme";
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

        Theme::uses('default'); // oreo, huckbee
        Theme::set('title','Home');

        $data['info'] = 'Hello World';

        return Theme::view('index', $data);

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
}
