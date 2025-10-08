<?php

namespace App\Services;

use App\Models\Applicant;
use App\Notifications\SelectionResultNotification;

class SelectionNotifier
{
    public static function notify($applicant, string $stage, string $result, string $status)
    {
        if ($applicant->user) { // pastikan ada relasi ke user
            $applicant->user->notify(
                new SelectionResultNotification($stage, $result, $status)
            );
        }
    }
}
