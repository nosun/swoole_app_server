<?php
/**
 * Created by PhpStorm.
 * User: lancelot
 * Date: 16-11-14
 * Time: 上午12:03
 */
namespace ctrl;

use cache\Cache;
use common\Utils;
use redis\Redis;
use ZPHP\Controller\IController;
use ZPHP\Core\Config;
use ZPHP\Protocol\Request;

class Base implements  IController
{

    /**
     * 业务逻辑开始前执行
     */
    function _before()
    {
        $path_info = Request::getPathInfo();

        if( in_array($path_info , Config::get('white_list')) )
        {
            return true;
        }
        $params = Request::getParams();
        if( !isset($params['uid']) )
        {
            return false;
        }
        $app_key = Redis::getInstance()->get('user_' . $params['uid']);
        return Utils::checkSign($app_key, $params);;
    }

    /**
     * 业务逻辑结束后执行
     */
    function _after()
    {
        // TODO: Implement _after() method.

    }
}