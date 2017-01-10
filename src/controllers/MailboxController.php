<?php

namespace Dooplenty\SyncSendRepeat\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Dooplenty\SyncSendRepeat\Jobs\SyncEmails;

class MailboxController extends Controller
{
    protected $mailbox;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function syncMessages()
    {
        //TODO connect info would be retrieved from db
        $username = env('SYNC_USER');
        $password = env('SYNC_PASS');
        $hostname = env('SYNC_HOSTNAME');
        $port = env('SYNC_PORT');
        $mailbox = env('SYNC_MAILBOX');
        $isSsl = env('SYNC_ISSSL') == '1' ? true : false;
        $protocol = env('SYNC_PROTOCOL');

        $configs = [
            'username' => $username,
            'password' => $password,
            'hostname' => $hostname,
            'port' => $port,
            'isSsl' => $isSsl,
            'protocol' => $protocol,
            'mailbox' => $mailbox
        ];

        $job = (new SyncEmails($configs));
        $this->dispatch($job);
    }

}
