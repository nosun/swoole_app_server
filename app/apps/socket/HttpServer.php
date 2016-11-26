<?php
/**
 * Created by PhpStorm.
 * User: lancelot
 * Date: 16-11-13
 * Time: 下午9:47
 */

namespace socket;

use cache\Cache;
use common\Constants;
use ZPHP\Core\Config;
use ZPHP\Socket\Callback\SwooleHttp;
use ZPHP\Protocol;
use ZPHP\Core;
use ZPHP\ZPHP;

class HttpServer extends SwooleHttp
{
    /**
     * @param \swoole_http_request $request
     * @param \swoole_http_response $response
     * @throws \Exception
     */
    public function onRequest($request, $response)
    {
        do {
            $path_info = explode("/", $request->server['path_info']);

            if (empty($path_info) || count($path_info) != 3) {
                $response->status(403);
                $response->end("");
                return;
            }
            $ctrl = $path_info[1];
            $method = $path_info[2];

            $rawContent = $request->rawContent();
            if (!empty($rawContent)) {
                Protocol\Request::parse($rawContent);
            }

            Protocol\Request::setCtrl($ctrl);
            Protocol\Request::setMethod($method);

            try {
                $content = Core\Route::route();
            } catch (\Exception $e) {
                $response->status(503);
                $content = \call_user_func(Config::getField('project', 'exception_handler', 'ZPHP\ZPHP::exceptionHandler'), $e);
            }

        } while (0);
        $response->end($content);
    }

    public static function before_start(\swoole_server $server)
    {
        // add extra worker process
        $process = new \swoole_process(function (\swoole_process $worker) use ($server) {

            $worker->name(Config::get('project_name') . ' worker process');
            Cache::getInstance()->setServer($server);
            Cache::getInstance()->refresh();
            
            swoole_timer_tick(Config::getField('worker_process', 'tick_interval'), function () use ($server) {
                Cache::getInstance()->refresh();
            });
        });
        $server->addProcess($process);
    }

    public function onWorkerStart($server, $workerId)
    {
        parent::onWorkerStart($server, $workerId);

        include dirname(__FILE__) . "/../functions.php";
        // reload config
        Config::load(ZPHP::getConfigPath());
    }

    public function onPipeMessage($server, $fromWorerId, $data)
    {
        $data = json_decode($data, true);

        switch($data['id'])
        {
            case Cache::CACHE_API:
            {
                Cache::getInstance()->setApiCache($data['data']);
                break;
            }
        }
    }

    public function onTask($server, $taskId, $fromId, $data)
    {

    }

    public function onFinish($server, $taskId, $data)
    {

    }
}