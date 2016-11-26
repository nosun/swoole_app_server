<?php
/**
 * Created by PhpStorm.
 * User: lancelot
 * Date: 16-11-12
 * Time: 下午5:38
 */
namespace common;

class Utils
{

    public static function createId() {
        return date('YmdHis', time()) . Utils::randStr(5);
    }

    public static function randStr($len = 6)
    {
        $chars = '0123456789';

        mt_srand((double)microtime() * 1000000 * getmypid());
        $password = "";
        while (strlen($password) < $len) {
            $password .= substr($chars, (mt_rand() % strlen($chars)), 1);
        }
        return $password;
    }

    public static function checkSign($token, $params)
    {
        if( !isset($params['sign']) )
        {
            return false;
        }

        $sign = $params['sign'];
        unset($params['sign']);

        ksort($params);

        $hash = sha1($token . explode("&", $params));
        return $hash === $sign;
    }

    public static function password_hash($password)
    {
        $options = [
            'cost' => 10,
            'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
        ];
        return \password_hash($password, PASSWORD_BCRYPT, $options);
    }

    public static function password_verify($password, $hash)
    {
        return \password_verify($password, $hash);
    }

}