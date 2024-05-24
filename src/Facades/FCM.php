<?php

namespace LaravelFCM\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void sendMessage($token, $title, $body, $data = null)
 * */

class FCM extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'fcm';
    }
}
