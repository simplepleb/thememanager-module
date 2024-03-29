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
 * @source https://github.com/simplepleb/article-module
 *
 * @license MIT For Premium Clients
 *
 * @since 1.0
 *
 */

namespace Modules\Thememanager\Entities;

use Illuminate\Database\Eloquent\Model;

// use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

use Spatie\Activitylog\Traits\LogsActivity;



class SiteTheme extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Notifiable;

    protected $table = 'site_themes';

    protected $fillable = [
      'slug',
      'active',
      'name',
      'settings',
      'page_styles',
      'page_blocks',
      'widgets',
      'custom_css',
      'custom_script',
      'custom_script',
      'created_by',
      'updated_by',
      'deleted_by',
    ];

    protected static $logName = 'themes';
    protected static $logOnlyDirty = true;
    protected static $logAttributes = ['name'];

    protected $casts = [
        'settings',
        'page_styles',
    ];


}
