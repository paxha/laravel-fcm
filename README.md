# Laravel FCM (Firebase Cloud Messaging) HTTP v1 API

[![Latest Stable Version](http://poser.pugx.org/paxha/laravel-fcm/v)](https://packagist.org/packages/paxha/laravel-fcm)
[![Total Downloads](http://poser.pugx.org/paxha/laravel-fcm/downloads)](https://packagist.org/packages/paxha/laravel-fcm)
[![Latest Unstable Version](http://poser.pugx.org/paxha/laravel-fcm/v/unstable)](https://packagist.org/packages/paxha/laravel-fcm)
[![License](http://poser.pugx.org/paxha/laravel-fcm/license)](https://packagist.org/packages/paxha/laravel-fcm)
[![PHP Version Require](http://poser.pugx.org/paxha/laravel-fcm/require/php)](https://packagist.org/packages/paxha/laravel-fcm)

## Introduction

This package provides ([Laravel Notification](https://laravel.com/docs/10.x/notifications)) **channels** for sending
notifications via FCM (Firebase Cloud Messaging) using **HTTP v1 API**. For more information about the Firebase Cloud
Messaging HTTP v1 API, please refer to the official
documentation: https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages

## Installation

To get started, you need to install the package via Composer:

```bash
composer require paxha/laravel-fcm
```

**Optional:** Once the package is installed, you can publish the configuration file using the following command:

```bash
php artisan vendor:publish --tag=fcm-config
```

This will create a new `fcm.php` file in your `config` directory, where you can configure your FCM settings. If you
don't publish it, it will create a default channel named `fcm` with the default configuration.

## Configuration

You can configure your FCM settings in the `fcm.php` file. Here's an example of the default configuration:

```php
'fcm' => [ // this fcm key is the channel name you can create multiple channels over here...
    'project' => env('GOOGLE_PROJECT'),
    'service_account' => env('GOOGLE_SERVICE_ACCOUNT'),
],
```

You can also set your FCM settings in your `.env` file:

Please put your service account file in the `storage/app` folder and set the filename in the `.env` file.

You can create a service account file from the Google Cloud Console. For more information, please refer to the
official: https://cloud.google.com/iam/docs/service-accounts-create

```dotenv
GOOGLE_PROJECT=your-project-id
GOOGLE_SERVICE_ACCOUNT=service-account.json
```

## Usage

To send a notification, you can use the channel provided by this package. Here's an example of how to send a
notification using the `fcm` channel:

Before sending the notification, you need to use our `HasPushToken` trait in your notifiable model. 

```php
class YourModel extends Model
{
    use \LaravelFCM\Traits\HasPushToken;
    
    // if your model has a push token column name other than `push_token` then you can define it like this
    public function routeNotificationForFCM()
    {
        return $this->your_custom_column; // column name where you stored the push token
    }
}
```

Create a new notification using the following command:

```bash
php artisan make:notification NewMessage
```

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;use Illuminate\Contracts\Queue\ShouldQueue;use Illuminate\Notifications\Notification;

class NewMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ?string $title = null,
        public ?string $body = null,
        public $data = null,
    ) {
    }

    public function via()
    {
        return ['fcm'];
    }

    public function toFCM()
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
        ];
    }
    
    // this is optional
    public function toData() {
        return $this->data;
    }
}
```

## Post Sending Notification

After sending the notification, it will call an event, and you can listen to the event to get the response of the
notification. And you can also do some other stuff after sending the notification.

```bash
php artisan make:listener PostNotificationListener
```

In your `EventServiceProvider`:

```php
protected $listen = [
    \LaravelFCM\Events\FCMNotificationSent::class => [
        PostNotificationListener::class,
    ],
];
```

In your `PostNotificationListener`:

```php
public function handle(object $event): void
{
    // you can the notifiable instance
    $notifiable = $event->notifiable;
    // you can get the notification instance
    $notification = $event->notification;
    // you can get the message instance. This is the message that was sent to FCM
    $message = $event->message;
     // you can get the response of the notification
    $response = $event->response;

    // you can also do some other stuff after sending the notification
}
```
