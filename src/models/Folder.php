<?php

namespace Dooplenty\SyncSendRepeat\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $table='ssr_folders';

    protected $fillable=[
    	'folder', 'config_id'
    ];
}
