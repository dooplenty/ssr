<?php

namespace Dooplenty\SyncSendRepeat\Commands;

use Dooplenty\SyncSendRepeat\Jobs\SyncEmails;

use Illuminate\Console\Command;

class QueueSyncJob extends Command
{
	use \Illuminate\Foundation\Bus\DispatchesJobs;

	/**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ssr:queue_sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue email sync jobs for a particular user.';

    public function handle()
    {
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