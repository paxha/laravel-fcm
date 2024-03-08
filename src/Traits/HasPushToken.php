<?php

namespace LaravelFCM\Traits;

trait HasPushToken
{
    /*
     * Your model must have a push_token field in order to work,
     * or you can override this method to define your own field
     * name to get the push token from.
     * */
    public function routeNotificationForFCM()
    {
        return $this->push_token;
    }
}
