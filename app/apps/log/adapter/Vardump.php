<?php
/**
 * Created by PhpStorm.
 * User: lidanyang
 * Date: 16/11/2
 * Time: 下午6:28
 */

namespace log\adapter;


use log\Logger;

class Vardump extends Logger
{

    protected function save($path, $content)
    {
        var_dump($content);
    }
}