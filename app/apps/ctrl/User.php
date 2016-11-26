<?php
/**
 * Created by PhpStorm.
 * User: lancelot
 * Date: 16-11-14
 * Time: 上午12:04
 */
namespace ctrl;

use cache\Cache;
use common\Error;
use common\Utils;
use log\Log;
use redis\Redis;
use ZPHP\Protocol\Request;

class User extends Base
{

    public function Login()
    {
        $param = Request::getParams();

        do {
            if( !isset($param['account']) || !isset($param['password']) )
            {
                $output['code'] = Error::ERR_INVALID_PARAMS;
                break;
            }

            $user_info = D('User')->field('uid', 'password')->where(['account' => $param['account']])->select();

            if( !Utils::password_verify( $param['password'], $user_info['password']) )
            {
                $output['code'] = Error::ERR_INVALID_PASSWORD;
                break;
            }

            $info['login_time'] = time();
            $info['uid'] = $user_info['uid'];
            D('User')->save($info);

            $app_key = hash_hmac("sha256", "AppServer{$info['login_time']}{$param['account']}", $user_info['password']);
            Redis::getInstance()->set('user_' . $info['uid'], $app_key);

            $output['data'] = [
                'app_key' => $app_key,
                'uid'    => $user_info['uid']
            ];
            $output['code'] = Error::SUCCESS;
        } while(0);

        return $output;
    }

    public function getList()
    {
        do {
            $redis = Redis::getInstance()->getConnection();
            Cache::getInstance();

            try {
                $cache = $redis->get("book_list");
                if( !$cache ) {
                    $result = D('Book')->select();
                    $redis->set("book_list", json_encode($result));
                    $redis->expire("book_list", 60);
                } else {
                    $result = json_decode($cache, true);
                }
            } catch (\Exception $e) {
                $output['code'] = $e->getCode();
                $output['msg']= $e->getMessage();
                Log::ERROR("Exception", $output);
                break;
            }

            if(empty($result))
            {
                $output['code'] = Error::ERR_ID_NOT_FOUND;
                $output['msg'] = "No Record";
                break;
            }

            $output['data'] = $result;
            $output['code'] = Error::SUCCESS;
        } while(0);

        return $output;
    }

    public function sendMsg()
    {
        $param = Request::getParams();

        do {
            if (!isset($param['sendTo']) || !isset($param['msg'])) {
                $output['code'] = Error::ERR_INVALID_PARAMS;
                break;
            }
            $swoole_http_server =  Request::getSocket();
            
            Request::getSocket()->task(json_encode([
                    'sendTo'    => $param['sendTo'],
                    'msg'       => $param['msg'],
                ]
            ));

            $output['code'] = Error::SUCCESS;
        }while(0);

        return $output;
    }

}