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

            // TODO
            // оно не ищет сложный паттерн с параметрами, хотя паттерн подходит, если смотреть в regex101.com
            // @^/news/(?<id>[0-9]+)/$@D
            // /news/13/
            // там ищет, а тут нет

            foreach($router->routes as $route) {
                if (preg_match($route["regex"], $url, $matches)) {
                    // TODO убрать потом
                    // если найден путь, показывает паттерн и что найдено
                    echo $route["regex"] . " => ";
                    print_r($matches);
                    echo " || ";
                    //

                    $controllerName = 'app\\controllers\\' . $route["controllerName"] . "Controller";
                    $controllerMethod = $route["controllerMethod"];

                    $object = new $controllerName();
                    return $object->$controllerMethod();
                }
            }

            exit;

            // TODO убрать потом
            // если нет пути, показывает паттерн, для которого не найдено и сам путь
            for ($i = 0; $i < count($router->routes); $i++) {
                preg_match($router->routes[$i]["regex"], $url, $matches);
                echo $router->routes[$i]["regex"] . " || ";
            }
            echo ' $url: ' . $url . "  ||  ";
            //

            throw new FrameworkException("Path not found");
        } else {
            throw new FrameworkException("REQUEST_URI not found");
        }
    }

    /**
     * Превратить путь вместе с параметрами в regex
     * @param $pattern
     * @param $params
     * @return bool|string
     */
    private function getRegex($pattern, $params)
    {
        if (preg_match('/[^-:\/_{}()a-zA-Z\d]/', $pattern)) {
            return false; // Неправильный паттерн пути
        }

        /**
         * Заменяем каждый параметр в паттерне на "(?<параметр>[правило_параметра]+)"
         * если в regex будет <что-то>, то оно просто исчезает (???), поэтому < меняем на &amp;lt;
         * и потом переделываем обратно с помощью htmlspecialchars_decode в return
         */
        foreach ($params as $key => $value) {
            $pattern = preg_replace(
                '/{' . $key . '}/',
                '(?<' . $key . '>' . $value . ')',
                $pattern
            );
        }

        /**
         * Превращаем в полноценный regex, преобразовывая всякие
         * &amp;lt в нормальные символы
         */
        return "@^" . htmlspecialchars_decode($pattern) . "$@D";
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
