<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostRemoved extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $postContent,
        private readonly string $reason,
        private readonly ?string $customReason = null,
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
            'post_content' => $this->postContent,
            'reason' => $this->reason,
            'custom_reason' => $this->customReason,
        ];
    }
}
