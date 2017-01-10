<?php

namespace Dooplenty\SyncSendRepeat\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'ssr_contacts';

    protected $fillable = [
    	'first_name', 'last_name', 'email', 'created_by', 'confirmed'
    ];
}
