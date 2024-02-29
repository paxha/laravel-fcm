# Laravel FCM (Firebase Cloud Messaging) HTTP v1 API

[![Latest Stable Version](http://poser.pugx.org/paxha/laravel-fcm/v)](https://packagist.org/packages/paxha/laravel-fcm)
[![Total Downloads](http://poser.pugx.org/paxha/laravel-fcm/downloads)](https://packagist.org/packages/paxha/laravel-fcm)
[![Latest Unstable Version](http://poser.pugx.org/paxha/laravel-fcm/v/unstable)](https://packagist.org/packages/paxha/laravel-fcm)
[![License](http://poser.pugx.org/paxha/laravel-fcm/license)](https://packagist.org/packages/paxha/laravel-fcm)
[![PHP Version Require](http://poser.pugx.org/paxha/laravel-fcm/require/php)](https://packagist.org/packages/paxha/laravel-fcm)

## Introduction

This package, Laravel FCM (Firebase Cloud Messaging), is designed to facilitate the sending of notifications using the
Firebase Cloud Messaging HTTP v1 API. It provides a simple and efficient way to handle FCM notifications within a
Laravel application. With this package, developers can easily integrate FCM notifications into their Laravel projects,
allowing for real-time updates and communication with users. The package leverages the power of Firebase's robust cloud
messaging service, making it easier to send targeted notifications to different user segments.

[//]: # (https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages)

## Installation

To get started, you need to install the package via Composer:

```bash
composer require paxha/laravel-fcm
```

**Optional:** Once the package is installed, you can publish the configuration file using the following command:

```bash
php artisan vendor:publish --tag=fcm-config
```

This will create a new `fcm.php` file in your `config` directory, where you can configure your FCM settings.

## Configuration

You can configure your FCM settings in the `fcm.php` file. Here's an example of the default configuration:

```php
'fcm' => [
    'project' => env('GOOGLE_PROJECT'),
    'service_account' => env('GOOGLE_SERVICE_ACCOUNT'),
],
```

You can also set your FCM settings in your `.env` file:

Please put your service account file in the `storage/app` folder and set the filename in the `.env` file.

```dotenv
GOOGLE_PROJECT=your-project-id
GOOGLE_SERVICE_ACCOUNT=service-account.json
```

## Usage

To send a notification, you can use the channel provided by this package. Here's an example of how to send a
notification using the `fcm` channel:

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
