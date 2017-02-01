<?php

namespace Dooplenty\SyncSendRepeat\Jobs;

use App\Jobs\Job;

use Dooplenty\SyncSendRepeat\Mailbox;
use Dooplenty\SyncSendRepeat\Message;
use Dooplenty\SyncSendRepeat\Models\Attachment as AtttachmentModel;
use Dooplenty\SyncSendRepeat\Models\Contact as ContactModel;
use Dooplenty\SyncSendRepeat\Models\Message as MessageModel;
use Dooplenty\SyncSendRepeat\Models\MessageAttributes as MessageAttributesModel;
use Dooplenty\SyncSendRepeat\Models\MessageContact as MessageContactModel;
use Dooplenty\SyncSendRepeat\Models\MessageContent as MessageContentModel;
use Dooplenty\SyncSendRepeat\ModelsRelationship as RelationshipModel;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use PDOException;
use Storage;

class SyncEmails extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $config;

    private $messageRelationships = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->getMessages();
    }

    protected function getMessages(array $msgnos = null)
    {

        $Mailbox = new Mailbox($this->config);

        $messages = $Mailbox->getMessages($msgnos);


        if (count($messages)) {

            foreach ($messages as $Message) {
                $this->getMessageData($Message);
            }

            if (count($this->messageRelationships)) {
                foreach ($this->messageRelationships as $parentMessageId => $syncedMessageId) {
                    $ExistingMessage = MessageModel::where('string_message_id',$parentMessageId)->get();

                    if ($ExistingMessage->count()) {
                        unset($this->messageRelationships[$parentMessageId]);

                        $this->setRelationship($ExistingMessage->first()->id, $Message->getMessageId());
                    }
                }
            }
        }
    }

    protected function setRelationship($parentMessageId, $childMessageId)
    {
        try {
            $RelationshipModel = new RelationshipModel([
                'message_id' => $childMessageId, 
                'parent_message_id' => $parentMessageId
            ]);

            $RelationshipModel->save();
        } catch (PDOException $e) {
            //TODO check for this being duplicate record exception
        }
    }

    protected function getMessageData(Message $Message)
    {

        if($this->messageExists($Message)) {
            return false;
        }

        $MessageModel = new MessageModel();

        $MessageModel->msgno = $Message->msgId;
        $MessageModel->string_message_id = $Message->string_message_id;
        $MessageModel->uid = $Message->uid;

        $MessageModel->save();

        $this->resolveOwnerOfEmail($Message, $MessageModel);

        $attachments = $Message->attachments;

        $this->saveMessageAttributes($MessageModel->attributes(), $Message, (bool)$attachments);

        $this->saveMessageAttachments($attachments, $MessageModel->attachments());

        $this->saveMessageContent($MessageModel->content(), $Message);

        if (isset($messageData->in_reply_to)) {
            $this->messageRelationships[$messageData->in_reply_to] = $Message->getMessageId();
        }

        if (isset($this->messageRelationships[$Message->msgId])) {
            $this->setRelationship($MessageModel->id, $this->messageRelationships[$Message->msgId]);
        }
    }

    /**
     * Check if message exists by uid
     * @param  Message $Message
     * @return boolean
     */
    protected function messageExists(Message $Message)
    {
        return MessageModel::where('uid', '=', $Message->uid)->exists();
    }

    protected function resolveOwnerOfEmail(Message $Message, MessageModel $MessageModel)
    {
        $emailAddresses = [$Message->fromAddress];

        $messageContacts = [];

        if ($Message->replyToAddresses) {
            foreach($Message->replyToAddresses as $replyToAddress) {
                array_push($emailAddresses, $replyToAddress['email']);
            }
        }

        $contacts = ContactModel::whereIn('email', $emailAddresses)->get();
        
        if ($contacts->count()) {
            $i = 0;

            foreach($contacts as $Contact) {
                $key = array_search($Contact->email, $emailAddresses);
                array_push($messageContacts, $Contact);
                unset($emailAddresses[$key]);
            }
        }

        foreach ($emailAddresses as $emailAddress) {
            try {
                $Contact = new ContactModel(['email' => $emailAddress]);
                $Contact->save();
                array_push($messageContacts, $Contact);
            } catch (\Illuminate\Database\QueryException $e) {
                //do smoething later maybe
            }
        }

        foreach ($messageContacts as $Contact) {
            try {
                $MessageContact = new MessageContactModel([
                    'contact_id' => $Contact->id,
                    'message_id' => $MessageModel->id,
                    'is_owner' => '1'
                ]);

                $MessageContact->save();
            } catch (\Illuminate\Database\QueryException $e) {
                //do something later (at least log)
            }
        }
    }

    protected function saveMessageContent(Relation $Content, Message $message)
    {
        $Content->save(new MessageContentModel([
            'text_body' => $message->textBody,
            'html_body' => $message->htmlBody
        ]));
    }

    protected function saveMessageAttributes(Relation $AttributesModel, Message $message, $hasAttachments)
    {
        $AttributesModel->save(new MessageAttributesModel([
            'from' => $message->fromAddress,
            'to' => join(",", array_map(function($a){ return $a['email']; }, $message->toAddresses)),
            'sent_date' => $message->sentDate,
            'subject' => $message->subject,
            'size' => $message->size,
            'in_reply_to' => $message->inReplyToAddress,
            'has_attachment' => $hasAttachments ? 1 : 0
        ]));
    }

    protected function saveMessageAttachments($attachments, Relation $AttachmentModel)
    {
        if ($attachments) {
            foreach ($attachments as $attachment) {
                $extension = pathinfo($attachment['filename'], PATHINFO_EXTENSION);

                $random = str_random(30);
                $new_filename = "$random.$extension";

                //TODO build this path from the user ssr/App::user()->id
                $path = sprintf("ssr/%d", $AttachmentModel->getParentKey());

                if (!Storage::disk('local')->exists($path)) {
                    Storage::disk('local')->makeDirectory($path, $mode=777, true, true);
                }

                $filepath = $path."/$new_filename";

                Storage::disk('local')->put($filepath, $attachment['content']);

                $mimeType = Storage::disk('local')->mimeType($filepath);

                $AttachmentModel->save(new AtttachmentModel([
                    'filename' => $new_filename,
                    'mimetype' => $mimeType,
                    'location' => $filepath,
                    'original_filename' => $attachment['filename']
                ]));
            }
        }
    }
}
