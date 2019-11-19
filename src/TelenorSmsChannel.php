<?php

namespace SolveCase\TelenorBulkSms;

use Illuminate\Notifications\Notification;

class TelenorSmsChannel {

    protected $client;

    public function __construct(TelenorSmsClient $client){
        $this->client = $client;
    }

    public function send($notificable, Notification $notification){
        $message = $notification->toTelenorSms($notificable);
        if(is_string($message)){
            $message = TelenorMessage::create($message);
        }
        if($message->toNotGiven()){
            if(!$to = $notificable->routeNotificationFor('telenorsms')){
                throw new \Exception('Reciver cannot be null.');
            }
            $message->to($to);
        }

        $this->client->send($message->toArray());
    }
}