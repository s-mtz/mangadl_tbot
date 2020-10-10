<?php

namespace Route;

use App\Controller\Bot as ControllerBot;
use App\Controller\Controller as ControllerController;
use App\Controller\Queue as ControllerContQueue;
use App\Controller\Payment as ControllerPayment;

use FastRoute;

class Route
{
    private $dispatcher;
    private $controller;
    private $bot;
    private $Payment;
    private $queue;

    public function __construct()
    {
        $this->controller = new ControllerController();
        $this->bot = new ControllerBot();
        $this->queue = new ControllerContQueue();
        $this->Payment = new ControllerPayment();
    }

    public function start()
    {
        $this->dispatcher = FastRoute\simpleDispatcher([&$this, 'init']);
        $route_info = $this->dispatch();
        return $this->handle($route_info);
    }

    public function init(FastRoute\RouteCollector $r)
    {
        $r->addRoute('GET', '/payment', [&$this->Payment, 'payment']);
        $r->addRoute('GET', '/', [&$this->controller, 'home']);
        $r->addRoute('POST', '/bot', [&$this->bot, 'start']);
        $r->addRoute('POST', '/queue/{count:\d+}', [&$this->queue, 'run']);
    }

    public function dispatch()
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        // Strip query string (?foo=bar) and decode URI
        if (false !== ($pos = strpos($uri, '?'))) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $route_info = $this->dispatcher->dispatch($httpMethod, $uri);
        return $route_info;
    }

    public function handle($route_info)
    {
        switch ($route_info[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                return null;
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                // $allowedMethods = $route_info[1];
                return null;
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $route_info[1];
                $vars = $route_info[2];
                $parameter = isset($vars[key($vars)]) ? $vars[key($vars)] : null;
                return $handler($parameter);
                break;
        }
    }
}
