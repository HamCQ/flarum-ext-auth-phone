<?php

namespace HamCQ\AuthPhone\Common;

use Exception;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;

use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use AlibabaCloud\Tea\Exception\TeaError;

use Darabonba\OpenApi\Models\Config;

use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Cache\Repository;

use HamCQ\AuthPhone\Common\GenerateCode;

use HamCQ\AuthPhone\KeyDisk;
use HamCQ\AuthPhone\Users;
use Flarum\Foundation\ValidationException;
use Symfony\Contracts\Translation\TranslatorInterface;
class AliSMS 
{
    public static function createClient($accessKeyId, $accessKeySecret){
        $config = new Config([
            "accessKeyId" => $accessKeyId,
            "accessKeySecret" => $accessKeySecret
        ]);
        // 访问的域名
        $config->endpoint = "dysmsapi.aliyuncs.com";
        return new Dysmsapi($config);
    }

    public static function send($data, $uid, $ip){
        $msg = ["status" => false , "msg" => ""];
        $phone = isset($data["phone"]) ? $data["phone"] : 0;
        $region = isset($data["region"]) ? $data["region"] : "";
        if (!$phone || !$region){
            $msg["msg"] = "param is invalid";
            return $msg;
        }
        $translator = resolve(TranslatorInterface::class);
        $settings = resolve(SettingsRepositoryInterface::class);
        $temp_code = $settings->get('flarum-ext-auth-phone.sms_ali_template_code');

        if(!in_array($region,["ChineseMainland","HongKong","Macao","Taiwan"])){
            throw new ValidationException(["msg"=>$translator->trans('hamcq-auth-phone.forum.alerts.region_invalid')]);
        }
        if($region!="ChineseMainland"){
            $regionInfo = ["ChineseMainland"=>"86","HongKong"=>"852","Macao"=>"853","Taiwan"=>"886"];
            $phone = $regionInfo[$region].$phone;
            $temp_code = $settings->get('flarum-ext-auth-phone.api_sms_ali_template_code_traditional');
        }   
        if(self::phoneExist($phone)){
            $msg["status"] = false;
            $msg["msg"] = "phone_exist";
            return $msg;
        }

        $generate = resolve(GenerateCode::class);
        $second = $settings->get('flarum-ext-auth-phone.sms_ali_expire_second');
        list($res, $status) = $generate->generate($uid, $phone, $second, $ip);
        if ($status){
            $msg["msg"] = "code_exist";
            $msg["time"] = ceil(($res - time())/60);
            return $msg;
        }
        $client = self::createClient(
            $settings->get('flarum-ext-auth-phone.sms_ali_access_id'), 
            $settings->get('flarum-ext-auth-phone.sms_ali_access_sec')
        );
        $sendSmsRequest = new SendSmsRequest([
            "signName" => $settings->get('flarum-ext-auth-phone.sms_ali_sign'),
            "templateCode" => $temp_code,
            "phoneNumbers" => $phone,
            "templateParam" => "{\"code\":\"".$res."\"}"
        ]);
        try {
            // https://help.aliyun.com/document_detail/55288.html
            $res = $client->sendSmsWithOptions($sendSmsRequest, new RuntimeOptions([]));
            if (isset($res->statusCode) && $res->statusCode!=200){
                app('log')->info( $res->statusCode );
                $msg["status"] = false;
                $msg["msg"] = "Aliyun API Error";
                return $msg;
            }
           
            if (isset($res->body->code) && strtolower($res->body->code)!="ok"){
                app('log')->info( $res->body->code );
                app('log')->info( $res->body->message );
                app('log')->info( $res->body->requestId );
                
                $msg["status"] = false;
                $msg["msg"] = $res->body->message;
                return $msg;
            }

            $msg["status"] = true;
            return $msg;
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
            }
            app('log')->error( $error->message );
            $msg["msg"] = $error->message;
            return $msg;
        }
    }

    public static function phoneExist($phone){
        $disk = resolve(KeyDisk::class);
        $info = $disk->get();
        $en_phone = (new Aes($info["key"],$info["iv"]))->Encrypt($phone);
        $query = Users::select("id","phone")->where(["phone"=>$en_phone])->first();
        if($query){
            return true;
        }
        return false;
    }

}