<?php

namespace App\Mail;

use App\Models\Applicant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SelectionResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectLine;
    public string $body;
    public string $stage;
    public string $type;
    public Applicant $applicant;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subjectLine, string $body, string $stage, string $type, ?Applicant $applicant = null)
    {
        $this->subjectLine = $subjectLine;
        $this->body        = $body;
        $this->stage       = $stage;
        $this->type        = $type;
        $this->applicant   = $applicant ?? new Applicant();
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->view('emails.selection_result') // ✅ gunakan blade kita
                    ->with([
                        'subject'   => $this->subjectLine,
                        'body'      => $this->body,
                        'stage'     => $this->stage,
                        'type'      => $this->type,
                        'applicant' => $this->applicant,
                    ]);
    }
}
