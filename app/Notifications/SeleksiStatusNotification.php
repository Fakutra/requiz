<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SeleksiStatusNotification extends Notification
{
    use Queueable;

    protected string $stage;
    protected string $status;

    public function __construct(string $stage, string $status)
    {
        $this->stage  = $stage;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Status seleksi Anda pada tahap {$this->stage} : {$this->status}",
            'stage'   => $this->stage,
            'status'  => $this->status,
            'link'    => route('history.index'), // arahkan ke halaman history peserta
        ];
    }
}
