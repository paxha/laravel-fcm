<?php

namespace LaravelFCM\Events;

class FCMNotificationSent
{
    public $notifiable;
    public $notification;
    public $message;
    public $response;

    public function __construct($notifiable, $notification, $message, $response)
    {
        $this->notifiable = $notifiable;
        $this->notification = $notification;
        $this->message = $message;
        $this->response = $response;
    }
}
