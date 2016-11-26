<?php
/**
 * Created by PhpStorm.
 * User: lancelot
 * Date: 16-11-20
 * Time: 下午11:21
 */
namespace model;

use ZPHP\Core\Config;

class BaseDB
{
    /**
     * @var array[]
     */
    private static $instance = [];


    public static function getInstance($name)
    {
        if( self::$instance[$name] == null ) {
            self::$instance[$name] = new BaseDB($name);
        }

        return self::$instance[$name];
    }

    private $pdo;
    private $config;

    public function __construct($name)
    {
        if( empty($name) ) {
            $name = 'mysql';
        }
        $this->config = Config::getField('pdo', $name);

        $this->connect();
    }

    private function connect()
    {
        try {
            $this->pdo = new \PDO($this->config['dsn'], $this->config['user'], $this->config['password'], $this->config['option']);
        } catch (\Exception $e ) {

        }
        return $this->pdo;
    }

    public function getPdo()
    {
        try {
            $status = $this->pdo->getAttribute(\PDO::ATTR_SERVER_INFO);
        } catch (\Exception $e) {
            if ($e->getCode() == 'HY000') {
                $this->pdo = $this->connect();
            } else {
                throw $e;
            }
        }
        return $this->pdo;
    }
}