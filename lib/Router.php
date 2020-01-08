<?php


namespace lib;
use lib\exceptions\FrameworkException;

class Router
{
    /**
     * @var Router
     */
    protected static $instance;
    protected $routes = [];

    public static function getInstance(): self
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Превратить путь вместе с параметрами в regex
     * @param $pattern
     * @param $params
     * @return bool|string
     */
    private function getRegex($pattern, $params){
        if (preg_match('/[^-:\/_{}()a-zA-Z\d]/', $pattern)) {
            return false; // Неправильный паттерн пути
        }

        // Заменяем каждый параметр в паттерне на "(?<параметр>[правило_параметра]+)"
        foreach ($params as $key => $value) {
            $pattern = preg_replace(
                '/{' . $key . '}/',
                '(?&amp;lt;' . $key . '>' . $value . ')',
                $pattern
            );
        }

        // Превращаем в полноценный regex
        return "@^" . htmlspecialchars_decode($pattern) . "$@D";
    }

    private function getMaskArray($mask){
        if (!preg_match('/^.+\..+$/', $mask)) {
            return false; // Неправильный паттерн маски
        }

        return explode(".", $mask);
    }

    /**
     * Добавляем маршрут в массив маршрутов
     * @param string $uri
     * @param string $mask
     * @param array $params
     * @return Router
     */
    public static function registerRoute(string $uri, string $mask, array $params = []): self
    {
        $router = static::getInstance();
        $regex = $router->getRegex($uri, $params);

        $maskArray = $router->getMaskArray($mask);

        $router->routes[] = array("regex" => $regex, "controllerName" => $maskArray[0], "controllerMethod" => $maskArray[1]);

        // TODO
        // Не подключает контроллер

        $controller = "app/controllers/{$maskArray[0]}Controller.php";
        require_once($controller);

        return $router;
    }

    /**
     * Вызываем метод контроллера конкретного маршрута
     * @return string результат выполнения метода
     * @throws FrameworkException
     */
    public function route(): string
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $path = $_SERVER['REQUEST_URI'];
            $router = static::getInstance();

            // TODO
            // оно не ищет сложный паттерн с параметрами, хотя паттерн подходит

            for ($i = 0; $i < count($router->routes); $i++) {
                if(preg_match($router->routes[$i]["regex"], $path)) {

                    echo "YES ".$i." ".$router->routes[$i]["regex"]." ; ";

                    // TODO
                    // не выполняет метод класса

                    $controllerName = $router->routes[$i]["controllerName"];
                    $controllerClass = $router->routes[$i]["controllerMethod"];
                    return ($controllerName."Controller")->$controllerClass();;
                }
            }

            throw new FrameworkException("Path not found");
        } else {
            throw new FrameworkException("REQUEST_URI not found");
        }
    }

    protected function __construct()
    {
    }
}
