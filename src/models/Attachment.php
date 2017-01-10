<?php

namespace Dooplenty\SyncSendRepeat\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table='ssr_message_attachments';

    protected $fillable=[
    	'original_filename', 'filename', 'mimetype', 'downloaded', 'location', 'message_id'
    ];
}
