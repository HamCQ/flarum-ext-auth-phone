<?php

namespace  HamCQ\AuthPhone\Listener;

use HamCQ\AuthPhone\Common\Aes;
use Flarum\User\Event\Saving;
use Illuminate\Support\Arr;
use Flarum\Foundation\ValidationException;
use HamCQ\AuthPhone\Common\AliSMS;
use Illuminate\Contracts\Cache\Repository;
use HamCQ\AuthPhone\PhoneHistory;
use HamCQ\AuthPhone\KeyDisk;
use HamCQ\AuthPhone\PhoneCode;
use Symfony\Contracts\Translation\TranslatorInterface;

class SavePhone
{
    // protected $cache;

    // public function __construct(Repository $cache)
    // {
    //     $this->cache = $cache;
    // }

    public function handle(Saving $event)
    {
        $user = $event->user;
        $data = $event->data;
        $actor = $event->actor;
        $isSelf = $actor->id === $user->id;
        $canEdit = $actor->can('edit', $user);
        $attributes = Arr::get($data, 'attributes', []);
        if ( isset($attributes['phone']) ) {
            if (!$isSelf) {
                $actor->assertPermission($canEdit);
            }
            $disk = resolve(KeyDisk::class);
            $info = $disk->get();
            if ($attributes['phone']==""){
                PhoneHistory::insert([
                    "user_id" => $user->id,
                    "phone" => $user->phone,
                    "created_time" => time()
                ]);
                $user->phone = "";
                $user->save();
                return;
            }
            if(!isset($attributes['code']) || $attributes['code']==""){
                throw new ValidationException(["msg" => "code_null"]);
            }
            $encryptPhone = (new Aes($info["key"],$info["iv"]))->Encrypt($attributes['phone']);
            $info = PhoneCode::where([
                ["user_id", "=", $user->id],
                ["phone", "=", $encryptPhone],
                ["exp_time",">=", time()]
            ])->orderBy("created_time","desc")->first();
            $translator = resolve(TranslatorInterface::class);
            if(!$info){
                throw new ValidationException(["msg"=>$translator->trans('hamcq-auth-phone.forum.alerts.code_expired')]);
            }
            // $code = $this->cache->get($user->id."_".trim($attributes['phone']));
            // if(!$code){
            //     app('log')->info( "code_expired: userid:".$user->id." cacheCode:[".$code."] attrCode[".$attributes['code']."]" );
            //     throw new ValidationException(["msg"=>"code_expired"]);
            // }
            if($info->code!=$attributes['code']){
                // app('log')->info( "code_invalid: userid:".$user->id." cacheCode:[".$code."] attrCode[".$attributes['code']."]" );
                throw new ValidationException(["msg"=>$translator->trans('hamcq-auth-phone.forum.alerts.code_invalid')]);
            }
            if (AliSMS::phoneExist($attributes['phone']) ){
                throw new ValidationException(["msg"=>$translator->trans('hamcq-auth-phone.forum.alerts.phone_exist')]);
            }
            // $this->cache->delete($user->id."_".$attributes['phone']);
            // $this->cache->delete($attributes['phone']."_time");
            $user->phone = $encryptPhone;
            $user->save();
        }
    }
}