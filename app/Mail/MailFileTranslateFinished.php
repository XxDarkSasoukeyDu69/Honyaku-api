<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailFileTranslateFinished extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $messageContent;
    public $content;
    public $file;
    public $fileType;

    /**
     * Create a new message instance.
     *
     * @param $message
     * @param $content
     * @param $file
     * @param $fileType
     */
    public function __construct($message, $content, $file, $fileType)
    {
        $this->messageContent = $message;
        $this->content = $content;
        $this->file = $file;
        $this->fileType = $fileType;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Votre traduction est finie !')
            ->view('emails.MailFileTranslateFinished')
            ->from('honyakuca@gmail.com')
            ->attachData($this->content, ''.$this->file['targetLang'].'.'.$this->fileType.'');
    }
}
