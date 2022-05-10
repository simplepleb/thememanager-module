<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Feature;
use App\Models\Permission;
use Modules\Thememanager\Entities\SiteTheme;
use Modules\Thememanager\Http\Controllers\Backend\ThememanagerController;

class CreateSiteThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_themes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->json('settings')->nullable();
            $table->json('page_styles')->nullable();
            $table->json('page_blocks')->nullable();
            $table->json('widgets')->nullable();
            $table->text('custom_css')->nullable();
            $table->text('custom_script')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        $themes = new ThememanagerController();
        $themes->refresh(true);
        SiteTheme::where('slug','default')->update(['active' => 1]);

        $feature = Feature::where('feature_name', 'thememanager')->first();
        if( !$feature ){
            $feature = new Feature();
            $feature->feature_name = 'thememanager';
            $feature->description = 'Theme Manager';
            $feature->created_at = now();
            $feature->save();
        }

        Permission::insert([

            ['name' => 'add themes', 'display_name' => 'Add Themes', 'feature_id' => $feature->id],
            ['name' => 'view themes', 'display_name' => 'View Themes', 'feature_id' => $feature->id],
            ['name' => 'edit themes', 'display_name' => 'Edit Themes', 'feature_id' => $feature->id],
            ['name' => 'delete themes', 'display_name' => 'Delete Themes', 'feature_id' => $feature->id],

        ]);

        $permissions = Permission::where('feature_id',$feature->id )->get();
        $super = \App\Models\Role::where('name', 'Super Admin')->first();
        $admin = \App\Models\Role::where('name', 'Admin')->first();

        foreach( $permissions as $permission ){
            $super->givePermissionTo($permission);
            $admin->givePermissionTo($permission);
        }

        $installed_file = storage_path('installed.txt');
        fopen($installed_file, "w");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_themes');
    }
}
