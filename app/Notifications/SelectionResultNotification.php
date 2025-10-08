<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SelectionResultNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $stage;
    public string $result;
    public string $status;

    public function __construct(string $stage, string $result, string $status)
    {
        $this->stage = $stage;
        $this->result = $result;
        $this->status = $status;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // bisa ditambah 'mail' kalau mau kirim email juga
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Hasil {$this->stage}",
            'message' => $this->result === 'lolos'
                ? "Selamat, Anda lolos tahap {$this->stage}. Status Anda sekarang: {$this->status}."
                : "Mohon maaf, Anda tidak lolos tahap {$this->stage}.",
            'status' => $this->status,
        ];
    }
}
