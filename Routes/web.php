<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::prefix('thememanager')->group(function() {
    // Route::get('/', '\Modules\Thememanager\Http\Controllers\Backend\ThememanagerController@index');

    // Used for testing and preview new themes

});*/

/*Route::group(['namespace' => '\Modules\Thememanager\Http\Controllers\Frontend', 'as' => 'frontend.', 'middleware' => 'web', 'prefix' => ''], function () {
    Route::get('/preview', '\Modules\Thememanager\Http\Controllers\Frontend\ThememanagerController@index');
});
*/

/*
*
* Backend Routes
*
* --------------------------------------------------------------------
*/
Route::group(['namespace' => '\Modules\Thememanager\Http\Controllers\Backend', 'as' => 'backend.', 'middleware' => ['web', 'auth', 'can:view_backend'], 'prefix' => 'admin'], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Posts Routes
     *
     * ---------------------------------------------------------------------
     */
    $module_name = 'thememanager';
    $controller_name = 'ThememanagerController';
    Route::get("$module_name/index_list", ['as' => "$module_name.index_list", 'uses' => "$controller_name@index_list"]);
    Route::get("$module_name/index_data", ['as' => "$module_name.index_data", 'uses' => "$controller_name@index_data"]);
    Route::get("$module_name/trashed", ['as' => "$module_name.trashed", 'uses' => "$controller_name@trashed"]);
    Route::patch("$module_name/trashed/{id}", ['as' => "$module_name.restore", 'uses' => "$controller_name@restore"]);
    Route::get("$module_name/disable", ['as' => "$module_name.disable", 'uses' => "$controller_name@disable"]);
    Route::post("$module_name/activate_theme", ['as' => "$module_name.activate_theme", 'uses' => "$controller_name@activate_theme"]);
    // Route::post("$module_name/preview", ['as' => "$module_name.preview", 'uses' => "$controller_name@preview"]);
    Route::get("$module_name/preview/{vname}/{name?}", '\Modules\Thememanager\Http\Controllers\Backend\ThememanagerController@preview');
    Route::get("$module_name/refresh", ['as' => "$module_name.refresh", 'uses' => "$controller_name@refresh"]);
    Route::get("$module_name/settings/{name}", ['as' => "$module_name.settings", 'uses' => "$controller_name@settings"]);

    Route::resource("$module_name", "$controller_name");


});

