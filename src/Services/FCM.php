<?php

namespace LaravelFCM\Services;

use Google\Client;
use Google\Exception;
use Google\Service\FirebaseCloudMessaging;
use Illuminate\Support\Facades\Storage;

class FCM
{
    public $client;

    public $httpClient;

    public $project;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->createClient(config('fcm.default'));
    }

    /**
     * @throws Exception
     */
    public function channel($channel): FCM
    {
        $this->createClient($channel);

        return $this;
    }

    /**
     * @throws Exception
     */
    private function createClient($channel)
    {
        $this->client = new Client();

        $serviceAccount = json_decode(file_get_contents(config_path("fcm.channels.$channel.service_account")), true);

        $this->client->setAuthConfig($serviceAccount);

        $this->client->addScope(FirebaseCloudMessaging::CLOUD_PLATFORM);

        $this->httpClient = $this->client->authorize();

        $this->project = config("fcm.channels.$channel.project");
    }

    public function sendMessage($token, $title, $body, $data = null)
    {
        $message = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
            ],
        ];

        if ($data) {
            $message['message']['data'] = $data;
        }

        return $this->httpClient->post("https://fcm.googleapis.com/v1/projects/$this->project/messages:send", [
            'json' => $message,
        ]);
    }
}
