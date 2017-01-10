<?php

namespace Dooplenty\SyncSendRepeat\Models;

use Illuminate\Database\Eloquent\Model;

class MessageAttributes extends Model
{
    protected $table='ssr_message_attributes';

    protected $fillable=[
    	'from', 'to', 'subject', 'sent_date', 'size', 'has_attachment', 'in_reply_to'
    ];
}
