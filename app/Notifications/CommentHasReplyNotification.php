<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CommentHasReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Comment $comment)
    {
    }

    public function via($notifiable)
    {
        if (!$notifiable->wantsNotification('receive_comment_reply_notifications')) {
            return [];
        }

        if ($this->comment->private) {
            return [];
        }

        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('notifications.new-reply-subject', ['title' => $this->comment->item->title]))
            ->greeting(trans('notifications.greeting', ['name' => $notifiable->name]))
            ->line(trans('notifications.new-reply-body'))
            ->action(trans('notifications.view-comment'), route('items.show', $this->comment->item) . '#comment-' . $this->comment->id)
            ->line(trans('notifications.unsubscribe-info'))
            ->salutation(trans('notifications.salutation') . "\n\r" . config('app.name'));
    }
}
