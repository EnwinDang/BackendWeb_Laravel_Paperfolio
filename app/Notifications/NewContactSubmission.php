<?php

namespace App\Notifications;

use App\Models\ContactSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewContactSubmission extends Notification
{
    use Queueable;

    public function __construct(
        private readonly ContactSubmission $submission,
    ) {
        //
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'submission_id' => $this->submission->id,
            'name' => $this->submission->name,
            'email' => $this->submission->email,
            'subject' => $this->submission->subject,
            'message' => $this->submission->message,
        ];
    }
}
