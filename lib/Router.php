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
     * @return Router
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

        // Подключаем контроллер
        require_once (
            dirname(__DIR__).DIRECTORY_SEPARATOR
            .'app'.DIRECTORY_SEPARATOR
            .'controllers'.DIRECTORY_SEPARATOR
            ."{$maskArray[0]}Controller.php"
        );

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
            $url = $this->url;
            $router = static::getInstance();

            // TODO
            // оно не ищет сложный паттерн с параметрами, хотя паттерн подходит, если смотреть в regex101.com
            // @^/news/(?<id>[0-9]+)/$@D
            // /news/13/
            // там ищет, а тут нет

//            echo " " . $url . "  ";

            for ($i = 0; $i < count($router->routes); $i++) {
                preg_match($router->routes[$i]["regex"], $url, $matches);
                echo " || " . $router->routes[$i]["regex"] . " => ";
                print_r($matches);

                if (preg_match($router->routes[$i]["regex"], $url)) {
//                    echo "YES ".$i." ".$router->routes[$i]["regex"]." ; ";

                    // TODO
                    // не выполняет метод класса

                    $controllerName = $router->routes[$i]["controllerName"]."Controller";
                    $controllerMethod = $router->routes[$i]["controllerMethod"];

//                    $object = new $controllerName();
                    $a = dirname(__DIR__).DIRECTORY_SEPARATOR
                        .'app'.DIRECTORY_SEPARATOR
                        .'controllers'.DIRECTORY_SEPARATOR
                        ."{$controllerName}";
                    $object = new $a();
//                    $object = new IndexController();

                    return $object->$controllerMethod();
                }
            }

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
        private function getRegex($pattern, $params){
        if (preg_match('/[^-:\/_{}()a-zA-Z\d]/', $pattern)) {
            return false; // Неправильный паттерн пути
        }

        // Заменяем каждый параметр в паттерне на "(?<параметр>[правило_параметра]+)"
        // если в regex будет <что-то>, то оно просто исчезает (???), поэтому < меняем на ?&amp;lt;
        // и потом переделываем обратно с помощью htmlspecialchars_decode в return
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

    protected function __construct()
    {
        $this->url = $_SERVER['REQUEST_URI'];
    }
}
