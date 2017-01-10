<?php

namespace Dooplenty\SyncSendRepeat\Models;

use Illuminate\Database\Eloquent\Model;

class MessageContact extends Model
{
    protected $table='ssr_message_contacts';

    protected $fillable=[
    	'message_id', 'contact_id', 'is_owner', 'is_recipient'
    ];

    public function message()
    {
    	return $this->belongsTo('Dooplenty\SyncSendRepeat\Message', 'message_id');
    }

    public function contact()
    {
    	return $this->belongsTo('Dooplenty\SyncSendRepeat\Contact', 'contact_id');
    }
}
