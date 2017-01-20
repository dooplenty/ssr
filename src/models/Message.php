<?php

namespace Dooplenty\SyncSendRepeat\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table='ssr_messages';

    protected $fillable=[
    	'msgno', 'string_message_id', 'uid'
    ];

    public function attachments()
    {
    	return $this->hasMany('\Dooplenty\SyncSendRepeat\Models\Attachment', 'message_id');
    }

    public function attributes()
    {
    	return $this->hasOne('\Dooplenty\SyncSendRepeat\Models\MessageAttributes', 'message_id');
    }

    public function content()
    {
    	return $this->hasOne('\Dooplenty\SyncSendRepeat\Models\MessageContent', 'message_id');
    }

    public function folder()
    {
    	return $this->belongsTo('\Dooplenty\SyncSendRepeat\Models\Folder');
    }
}
