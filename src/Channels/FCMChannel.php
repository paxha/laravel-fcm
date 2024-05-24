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
        $tokens = $notifiable->routeNotificationForFCM();

        if (empty($tokens)) {
            return;
        }

        $client = new Client();

        $serviceAccount = json_decode(file_get_contents($this->serviceAccount), true);

        $client->setAuthConfig($serviceAccount);

        $client->addScope(FirebaseCloudMessaging::CLOUD_PLATFORM);

        $httpClient = $client->authorize();

        $message = [
            'message' => [],
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
        
        if(! is_array($tokens)) {
            $tokens = [$tokens];
        }
        
        foreach($tokens as $token) {
            $message['message']['token'] = $token;
            
            $response = $httpClient->post("https://fcm.googleapis.com/v1/projects/$this->project/messages:send", ['json' => $message]);

            event(new FCMNotificationSent($notifiable, $notification, $message, $response));
        }
    }
}
