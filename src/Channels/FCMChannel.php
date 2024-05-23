<?php

namespace LaravelFCM\Channels;

use Google\Client;
use Google\Exception;
use Google\Service\FirebaseCloudMessaging;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Storage;
use LaravelFCM\Events\FCMNotificationSent;

class FCMChannel
{
    private $project;

    private $serviceAccount;

    public function __construct($project, $serviceAccount)
    {
        $this->project = $project;
        $this->serviceAccount = $serviceAccount;
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     * @throws \Exception
     */
    public function send($notifiable, $notification)
    {
        $token = $notifiable->routeNotificationForFCM();

        if (!$token) {
            return;
        }

        $client = new Client();

        $serviceAccount = file_get_contents($this->serviceAccount);

        $client->setAuthConfig($serviceAccount);

        $client->addScope(FirebaseCloudMessaging::CLOUD_PLATFORM);

        $httpClient = $client->authorize();

        $message = [
            'message' => [
                'token' => $token,
            ],
        ];

        if (method_exists($notification, 'toFCM')) {
            $message['message']['notification'] = $notification->toFCM();
        }
        if (method_exists($notification, 'toAPS')) {
            $message['message']['apns']['payload']['aps'] = $notification->toAPS();
        }
        if (method_exists($notification, 'toAndroid')) {
            $message['message']['android'] = $notification->toAndroid();
        }
        if (method_exists($notification, 'toWeb')) {
            $message['message']['webpush'] = $notification->toWeb();
        }
        if (method_exists($notification, 'toData')) {
            $message['message']['data'] = $notification->toData();
        }

        $response = $httpClient->post("https://fcm.googleapis.com/v1/projects/$this->project/messages:send", ['json' => $message]);

        event(new FCMNotificationSent($notifiable, $notification, $message, $response));
    }
}
