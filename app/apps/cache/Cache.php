<?php
/**
 * Created by PhpStorm.
 * User: lancelot
 * Date: 16-11-22
 * Time: 上午12:55
 */
namespace cache;

use ZPHP\Core\Config;

class Cache
{

    const CACHE_API = 1;

    private static $instance = null;

    public static function getInstance()
    {
        if( self::$instance == null )
        {
            self::$instance = new Cache();
        }
        return self::$instance;
    }

    /**
     * @var \swoole_server
     */
    private $server;

    private $api_cache;

    public function __construct()
    {

    }

    public function setServer(\swoole_server $server)
    {
        $this->server = $server;
    }

    public function refresh()
    {
        $result = file_get_contents("http://api.com/");

        $worker_num = Config::getField('socket', 'worker_num');
        $worker_num --;

        while($worker_num >= 0)
        {
            $data['id'] = self::CACHE_API;
            $data['data'] = $result;
            $this->server->sendMessage(json_encode($data), $worker_num);
            $worker_num --;
        }
    }

    /**
     * @return mixed
     */
    public function getApiCache()
    {
        return $this->api_cache;
    }

    /**
     * @param mixed $api_cache
     */
    public function setApiCache($api_cache)
    {
        $this->api_cache = $api_cache;
    }

}