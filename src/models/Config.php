<?php

namespace Dooplenty\SyncSendRepeat\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table='ssr_configs';

    protected $fillable=[
    	'username','password','hostname','port','protocol','mailbox','is_ssl'
    ];
}
