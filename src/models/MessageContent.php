<?php

namespace Dooplenty\SyncSendRepeat\Models;

use Illuminate\Database\Eloquent\Model;

class MessageContent extends Model
{
    protected $table='ssr_message_content';

    protected $fillable=[
    	'html_body', 'text_body'
    ];

    public function message()
    {
    	return $this->belongsTo('Message', 'message_id');
    }
}
