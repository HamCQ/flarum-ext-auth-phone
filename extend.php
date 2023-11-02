<?php

/*
 * This file is part of hamcq/flarum-ext-auth-phone.
 *
 * Copyright (c) 2022 Emin.lin(BG5UWQ).
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace HamCQ\AuthPhone;

use Flarum\Extend;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Api\Serializer\UserSerializer;
use FoF\Components\Extend\AddFofComponents;
use Flarum\User\Event\Saving;

use HamCQ\AuthPhone\Listener\SavePhone;
use HamCQ\AuthPhone\Middlewares\DiscussionMiddleware;
use HamCQ\AuthPhone\Console\BuildKeyCommand;

use Flarum\Foundation\Paths;
use Flarum\Http\UrlGenerator;

return [
    //需要引入 不然前端会报错
    new AddFofComponents(),

    //前端文件
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/resources/less/admin.less'),
    
    //翻译
    new Extend\Locales(__DIR__ . '/resources/locale'),

    //接口
    (new Extend\Routes('api'))
        ->post('/auth/sms/send', 'auth.sms.api.send', Controllers\SMSSendController::class),

    //发帖限制
    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attribute('canStartDiscussion', function (ForumSerializer $serializer) {
            if($serializer->getActor()->phone){
                return true;
            }
            return false;
    }),

    //接口限制
    (new Extend\Middleware('api'))->add(DiscussionMiddleware::class),

    //事件监听
    (new Extend\Event())->listen(Saving::class, SavePhone::class),

    //aes 秘钥存储
    (new Extend\Filesystem())
        ->disk('flarum-aes', function (Paths $paths) {
            return [
                'root'   => "$paths->storage/key",
            ];
    }),

    (new Extend\Console())->command(BuildKeyCommand::class),

    //初始化页面状态
    (new Extend\ApiSerializer(UserSerializer::class))
        ->attributes(function($serializer, $user, $attributes) {
            $isAuth = false;
            if ($user->phone){
                $isAuth = true;
            }
            $attributes['SMSAuth'] = [
                'isAuth' => $isAuth
            ];

            return $attributes;
        }),

    (new Extend\Settings())
        ->serializeToForum('hamcqAuthPhoneTips', 'hamcqAuthPhoneTips', 'boolVal')
        ->serializeToForum('hamcqAuthPhoneTipsOneTitle', 'hamcqAuthPhoneTipsOneTitle')
        ->serializeToForum('hamcqAuthPhoneTipsOneUrl', 'hamcqAuthPhoneTipsOneUrl')
        ->serializeToForum('hamcqAuthPhoneTipsTwoTitle', 'hamcqAuthPhoneTipsTwoTitle')
        ->serializeToForum('hamcqAuthPhoneTipsTwoUrl', 'hamcqAuthPhoneTipsTwoUrl')
        ->serializeToForum('hamcqAuthPhoneTipsThreeTitle', 'hamcqAuthPhoneTipsThreeTitle')
        ->serializeToForum('hamcqAuthPhoneTipsThreeUrl', 'hamcqAuthPhoneTipsThreeUrl')
        ,
];
