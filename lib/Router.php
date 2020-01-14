<?php


namespace lib;

use app\controllers\IndexController;
use lib\exceptions\FrameworkException;

class Router
{
    /**
     * @var Router
     */
    protected static $instance;
    protected $routes = [];
    protected $url;

    public static function getInstance(): self
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Добавляем маршрут в массив маршрутов
     * @param string $uri
     * @param string $mask
     * @param array $params
     * @return static
     */
    public static function registerRoute(string $uri, string $mask, array $params = []): self
    {
        // Получаем инстанс роутера
        $router = static::getInstance();

        // Парсим входные данные функции и получаем данные для добавления их в массив маршрутов
        $regex = $router->getRegex($uri, $params);
        $maskArray = $router->getMaskArray($mask);

        // Добавляем распарсенные данные в массив маршрутов
        $router->routes[] = array("regex" => $regex, "controllerName" => $maskArray[0], "controllerMethod" => $maskArray[1]);

        return $router;
    }

    /**
     * Вызываем метод контроллера конкретного маршрута
     * @return string результат выполнения метода
     * @throws FrameworkException
     */
    public function route(): string
    {
        header('Content-Type: text/plain');
        if (isset($_SERVER['REQUEST_URI'])) {
            $url = $this->url;
            $router = static::getInstance();

            foreach ($router->routes as $route) {
                if (preg_match($route["regex"], $url, $matches)) {
                    if (count($matches) > 1) {
                        $params = $this->getParams($matches);
                    }

                    $controllerName = 'app\\controllers\\' . $route["controllerName"] . "Controller";
                    $controllerMethod = $route["controllerMethod"];

                    $object = new $controllerName();

                    if (count($matches) > 1) {
                        return $object->$controllerMethod(...array_values($params));
                    } else {
                        return $object->$controllerMethod();
                    }
                }
            }

            throw new FrameworkException("Path not found");
        } else {
            throw new FrameworkException("REQUEST_URI not found");
        }
    }

    /**
     * @param $params
     * @param $values
     * @return Array
     */
    private function getParams($params): Array
    {
        $array = [];

        foreach ($params as $key => $value) {
            if (!preg_match("@^[0-9]$@", $key)) {
                $array[$key] = $value;
            }
        }

        return $array;

    }

    /**
     * Превратить путь вместе с параметрами в regex
     * @param $pattern
     * @param $params
     * @return string
     */
    private function getRegex($pattern, $params): string
    {
        // Заменяем каждый параметр в паттерне на "(?<параметр>[правило_параметра]+)"
        foreach ($params as $key => $value) {
            $pattern = preg_replace(
                '/{' . $key . '}/',
                '(?<' . $key . '>' . $value . ')',
                $pattern
            );
        }

        // Превращаем в полноценный regex
        return "@^" . $pattern . "$@D";
    }

    /**
     * Есть класс и метод, записанные как Class.Method
     * Делаем из этой строки массив
     * @param $mask
     * @return array|bool
     */
    private function getMaskArray($mask)
    {
        if (!preg_match('/^.+\..+$/', $mask)) {
            return false; // Неправильный паттерн маски
        }

        return explode(".", $mask);
    }

    protected function __construct()
    {
        $this->url = $_SERVER['REQUEST_URI'];
    }
}
