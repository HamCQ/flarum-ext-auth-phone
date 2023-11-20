<?php

namespace HamCQ\AuthPhone\Middlewares;

use Flarum\User\Event\Saving;
use Illuminate\Support\Arr;
use Flarum\Foundation\ValidationException;

class BioLimitMiddleware
{
    public function handle(Saving $event)
    {
        $user = $event->user;
        $data = $event->data;
        $actor = $event->actor;
        $attributes = Arr::get($data, 'attributes', []);
        if ( isset($attributes['bio']) && !$actor->phone) {
            throw new ValidationException(["msg"=>"Yikes! You need to verify your mobile number."]);
            return ;
        }

        if (Arr::has($attributes, 'socialButtons') && !$actor->phone) {
            throw new ValidationException(["msg"=>"Yikes! You need to verify your mobile number."]);
            return ;
        }

        if( isset($attributes['bio']) || Arr::has($attributes, 'socialButtons')){
            $user->save();
        }
    }

}