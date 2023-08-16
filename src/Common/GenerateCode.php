<?php
namespace HamCQ\AuthPhone\Common;

use Illuminate\Contracts\Cache\Repository;
use HamCQ\AuthPhone\PhoneCode;
use HamCQ\AuthPhone\KeyDisk;
use HamCQ\AuthPhone\Common\Aes;
class GenerateCode 
{

    // protected $cache;

    // public function __construct(Repository $cache)
    // {
    //     $this->cache = $cache;
    // }

    public function generate($uid, $phone, $second, $ip){
        if(!$second || $second==0){
            $second = 300;
        }
        $randNumber = mt_rand(100000,999999);
        str_shuffle($randNumber);
        $phone = trim($phone);
        $disk = resolve(KeyDisk::class);
        $diskInfo = $disk->get();
        $encryptPhone = (new Aes($diskInfo["key"],$diskInfo["iv"]))->Encrypt($phone);
        $info = PhoneCode::where([
            ["user_id", "=", $uid],
            ["phone", "=", $encryptPhone],
            ["exp_time", ">=", time()]
        ])->orderBy("created_time","desc")->first();

        // $status = $this->cache->get($uid."_".trim($phone));
        if($info){
            return array((int)$info->exp_time, true);
        }
        // $this->cache->put($uid."_".$phone, $randNumber, $second);
        // $this->cache->put($phone."_time", time() + $second, $second);

        PhoneCode::insert([
            "user_id" => $uid,
            "phone" => $encryptPhone,
            "code" => $randNumber,
            "ip" => $ip,
            "exp_time" => time() + $second,
            "created_time" => time()
        ]);
        
        return array($randNumber,false);
    }

    
}