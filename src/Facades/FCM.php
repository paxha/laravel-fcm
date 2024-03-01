<?php

namespace LaravelFCM\Facades;

use Illuminate\Support\Facades\Facade;

class FCM extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'fcm';
    }
}
