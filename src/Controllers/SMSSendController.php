<?php
namespace HamCQ\AuthPhone\Controllers;

use HamCQ\AuthPhone\Common\AliSMS;

use Flarum\Http\RequestUtil;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class SMSSendController implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertRegistered();
        if($actor->phone){
            $translator = resolve(TranslatorInterface::class);
            return new JsonResponse( ["status"=>false, "msg" => $translator->trans('hamcq-auth-phone.forum.alerts.already_linked')]);
        }
        $ip = $request->getAttribute('ipAddress');
        return new JsonResponse( AliSMS::send( $request->getParsedBody(), $actor->id, $ip) );
    }
}