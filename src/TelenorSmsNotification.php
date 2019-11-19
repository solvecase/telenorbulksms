<?php

namespace SolveCase\TelenorBulkSms;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use SolveCase\TelenorBulkSms\TelenorSmsChannel;

class TelenorSmsNotification extends Notification implements ShouldQueue{
    use Queueable;

    protected $message;

    public $retryAfter = 3;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TelenorSmsChannel::class];
    }

    public function toTelenorSms($notifiable)
    {
        return TelenorMessage::create()
        ->content($this->message);
    }
}