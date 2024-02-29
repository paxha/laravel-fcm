<?php

namespace LaravelFCM;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use LaravelFCM\Channels\FCMChannel;

class FCMServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/fcm.php', 'fcm');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/fcm.php' => config_path('fcm.php'),
        ], 'fcm-config');

        $this->registerNotificationChannels();
    }

    private function registerNotificationChannels()
    {
        foreach (config('fcm.channels') as $key => $channel) {
            Notification::extend($key, function () use ($channel) {
                return new FCMChannel($channel['project'], $channel['service_account']);
            });
        }
    }
}
