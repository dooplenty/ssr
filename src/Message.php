<?php
namespace Dooplenty\SyncSendRepeat;

use Dooplenty\SyncSendRepeat\Mailbox;
use Log;

class Message
{
	protected $headers;

	protected $attachments = [];

	//sequence id for this message
	protected $msgId;

	//message content
	protected $textBody;

	//html message content
	protected $htmlBody;

	//mailbox resource
	private $stream;

	protected $toAddresses;

	protected $ccAddresses;

	protected $fromAddress;

	protected $replyToAddresses;

	protected $inReplyToAddress;

	protected $subject;

	protected $string_message_id;

	protected $senderAddress;

	protected $size;

	protected $sentDate;

	public function __construct(Mailbox $Mailbox, $id)
	{
		$this->stream = $Mailbox->getStream();
		$this->msgId = $id;

		$this->setHeaderInfo();

		$structure = imap_fetchstructure($this->stream, $this->msgId);
		$this->textBody = $this->getPart('TEXT/PLAIN', $structure);
		$this->htmlBody = $this->getPart('TEXT/HTML', $structure);

		if(isset($structure->parts)) {
			foreach($structure->parts as $partNo => $part) {
				$this->loadAttachments($part, $partNo);
			}
		}
	}

	public function __get($var)
	{
		if(isset($this->$var)) {
			return $this->$var;
		}

		return false;
	}

	public function __isset($var)
	{
		if(!isset($this->$var)) {
			Log::warning("Variable $var is set on this class.");
			return false;
		}

		return true;
	}

	public function setHeaderInfo()
	{
		$headerInfo = imap_headerinfo($this->stream, $this->msgId);

		if($headerInfo) {
			if(isset($headerInfo->date)) {
				$this->sentDate = date('Y-m-d H:i:s', strtotime($headerInfo->date));
			}

			if(isset($headerInfo->to)) {
				foreach($headerInfo->to as $key => $to) {
					$this->toAddresses[] = [
						'email' => $to->mailbox . '@' . $to->host,
						'name' => isset($to->personal) ? $to->personal : $to->mailbox
					];
				}				
			}

			if(isset($headerInfo->from)) {
				$from = $headerInfo->from[0];
				$this->fromAddress = $from->mailbox . '@' . $from->host;
			}

			if(isset($headerInfo->reply_to)) {
				foreach($headerInfo->reply_to as $key => $replyTo) {
					$this->replyToAddresses[] = [
						'email' => $replyTo->mailbox . '@' . $replyTo->host,
						'name' => isset($replyTo->personal) ? $replyTo->personal : $replyTo->mailbox
					];
				}
			}

			if(isset($headerInfo->cc)) {
				foreach($headerInfo->cc as $key => $cc) {
					$this->ccAddresses[] = [
						'email' => $cc->mailbox . '@' . $cc->host,
						'name' => isset($cc->personal) ? $cc->personal : $cc->mailbox
					];
				}
			}

			if(isset($headerInfo->sender)) {
				$sender = $headerInfo->sender[0];
				$this->senderAddress = $sender->mailbox . '@' . $sender->host;
			}

			if(isset($headerInfo->in_reply_to)) {
				$this->inReplyToAddress = $headerInfo->in_reply_to;
			}

			$this->size = $headerInfo->Size;

			if(isset($headerInfo->subject)) {
				$subject = imap_mime_header_decode($headerInfo->subject);
				if($subject) {
					$this->subject = $subject[0]->text;
				}
			}

			$this->string_message_id = $headerInfo->message_id;
		}
	}

	protected function loadAttachments($part, $partNo)
	{
		$attachments = $this->attachments;

		if($part->ifdparameters) {
			foreach($part->dparameters as $parameter) {
				if(strtolower($parameter->attribute) == 'filename') {
					$attachments[$partNo]['is_attachment'] = true;
					$attachments[$partNo]['filename'] = $parameter->value;
				}
			}
		}

		if($part->ifparameters) {
			foreach($part->parameters as $parameter) {
				if(strtolower($parameter->attribute) == 'name') {
					$attachments[$partNo]['is_attachment'] = true;
					$attachments[$partNo]['name'] = $parameter->value;
				}
			}
		}

		if(isset($attachments[$partNo])) {
			$content = imap_fetchbody($this->stream, $this->msgId, $partNo+1);

			if($part->encoding == '3') {
				$content = base64_decode($content);
			} elseif($part->encoding == '4') {
				$content = quoted_printable_decode($content);
			}

			$attachments[$partNo]['content'] = $content;
		}

		$this->attachments = $attachments;

		if(isset($part->parts)) {
			foreach($part->parts as $subPartNo => $subPart) {
				$subNo = $subPartNo + 1;
				$this->loadAttachments($subPart, "$partNo.$subNo");
			}
		}
	}

	protected function getPart($mimeType, $structure, $partNo = null)
	{
		$prefix = null;
		if(!$structure) {
			return false;
		}

		if ($mimeType == $this->getMimeType($structure)) {
            $partNo = ($partNo > 0) ? $partNo : 1;

            return imap_fetchbody($this->stream, $this->msgId, $partNo);
        }

        if ($structure->type == 1) {
            foreach ($structure->parts as $index => $subStructure) {
                if ($partNo) {
                    $prefix = $partNo . '.';
                }

                $part = $this->getPart($mimeType, $subStructure, $prefix . ($index + 1));
                if ($part) {
                    return quoted_printable_decode($part);
                }
            }
        }
	}

    protected function getMimeType($structure)
    {
        $mimeTypes = array('TEXT', 'MULTIPART', 'MESSAGE', 'APPLICATION', 'AUDIO', 'IMAGE', 'VIDEO', 'OTHER');
        if ($structure->subtype)
        {
            return $mimeTypes[(int) $structure->type] . '/' . $structure->subtype;
        }

        return 'TEXT/PLAIN';
    }
}