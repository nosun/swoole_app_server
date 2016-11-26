<?php

return array(
    "project_name" => "app_server",
    'server_mode' => "Socket",
    'app_path'=>'apps',
    'ctrl_path'=>'ctrl',
    'project'=>array(
        'view_mode'=>'Json',
        'pid_path' => '/var/run/app_server',
        'config_check_time' => 3600,
    ),
    'socket' => array(
        'host' => '0.0.0.0',
        'port' => 8991,
        'daemonize' => 0,
        'work_mode' => 3,

        'ssl_cert_file' => __DIR__.'/../ssl/server.crt',
        'ssl_key_file' => __DIR__.'/../ssl/server.key',
        'ssl_client_cert_file' => __DIR__.'/../ssl/ca.crt',
        'ssl_verify_depth' => 10,

        // Work Process Config
        'worker_num' => 4,
        'dispatch_mode' => 3,
        'max_request' => 10000,

        // Task Worker Process Config
        'task_worker_num' => 4,
        'task_ipc_mode'=>3, // 争抢模式
        'task_max_request' => 10000,

        'adapter' => 'Swoole',
        'server_type' => 'https',
        'protocol' => 'Json',
        'call_mode' => 'ROUTE',
        'client_class' => 'socket\\HttpServer',

        'start_hook'   => 'socket\\HttpServer::before_start',
        'start_hook_args' => true,
    ),
    'worker_process' => array(
        'tick_interval' => 10000,
    )
);
