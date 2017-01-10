<?php
namespace Dooplenty\SyncSendRepeat;

use Dooplenty\SyncSendRepeat\Message;
use Log;

class Mailbox
{
	protected $stream;

	protected $messages = [];

	/**
	 * Do initial mailbox connection
	 * @param array $config connection params
	 */
	public function __construct($config)
	{
		list($username, $password, $hostname, $port, $isSsl, $protocol, $mailbox) = array_values($config);

		if(!$mailbox){
			$mailbox = 'INBOX';
		}

		$ssl = $isSsl ? "/ssl/novalidate-cert" : "/novalidate-cert";

		Log::info('Connecting ot mailbox');

		imap_timeout(IMAP_OPENTIMEOUT, 10);
		$resource = imap_open("{{$hostname}:{$port}/{$protocol}{$ssl}}$mailbox", $username, $password) or die(imap_last_error());

		Log::info('Connected ' . $resource);

		$this->stream = $resource;

		return $resource;
	}

	public function getMessages(array $msgnos = null, $criteria="ALL UNDELETED")
	{
		if(!$msgnos) {
			$msgnos = $this->getMessageIds($criteria);
		}

		if(!count($msgnos)) {
			return $this->messages;
		}

		foreach($msgnos as $msgno) {
			$Message = new Message($this, $msgno);
			$this->messages[$msgno] = $Message;
		}

		return $this->messages;
	}

	public function getStream()
	{
		return $this->stream;
	}

	protected function getMessageIds($criteria)
	{
		$msgnos = imap_search($this->stream, $criteria);
		return $msgnos;
	}
}