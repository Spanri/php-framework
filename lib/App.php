<?php


namespace lib;


use lib\exceptions\FatalFrameworkException;
use lib\exceptions\FrameworkException;

class App
{
    protected $config;

    public function run()
    {
        try {
            $this->init();
            echo "ROUTE: ".Router::getInstance()->route();
        } catch(FrameworkException $e) {
            echo "Framework Error: {$e->getMessage()}\n";
        } catch(\Throwable $e) {
            echo "Global error: {$e->getMessage()}\n";
        }

        exit;
    }

    protected function init(): self
    {
        $configPath = dirname(__DIR__).DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

        if(!file_exists($configPath)) {
            throw new FatalFrameworkException("file {$configPath} not exists");
        }

        $this->config = require $configPath;

        $initPath = dirname(__DIR__).DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'init.php';

        if(!file_exists($initPath)) {
            throw new FatalFrameworkException("file {$initPath} not exists");
        }

        require $initPath;

        return $this;
    }
}