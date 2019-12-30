<?php


namespace lib;


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
     * Добавляем маршрут в массив маршрутов, при этом ключ - uri, а все остальное добавляется
     * как ассоциативный массив к этому ключу. Например, так:
     * lib\Router Object
     *(
     *   [routes:protected] => Array
     *      (
     *         [/] => Array
     *              (
     *                  [mask] => Index.index
     *                  [params] => Array
     *                  (
     *                  )
     *              )
     *          [/other] => Array
     *              (
     *                  [mask] => Index.other
     *                  [params] => Array
     *                  (
     *                  )
     *              )
     *       )
     *)
     */
    public static function registerRoute(string $uri, string $mask, array $params = []): self
    {
        $router = static::getInstance();
        $router->routes[] = array("uri" => $uri, "mask" => $mask, "params" => $params);

        return $router;
    }

    private function getRegex($pattern, $params){
        if (preg_match('/[^-:\/_{}()a-zA-Z\d]/', $pattern))
            return false; // Invalid pattern
    
        // "(/)" в "/?"
        $pattern = preg_replace('#\(/\)#', '/?', $pattern);

        // echo " pattern ".$pattern."\n";
    
        // Заменяем {parameter} на (?<parameter>[a-zA-Z0-9\_\-]+)
        $allowedParamChars = preg_match('/{(.*)}/', $string, $matches);
        // $allowedParamChars = '[a-zA-Z0-9\_\-]+';

        // $pattern = preg_replace(
        //     '/{('. $allowedParamChars .')}/',    # Replace "{parameter}"
        //     '(?<${1}>'. $allowedParamChars . ')', # with "(?<parameter>[a-zA-Z0-9\_\-]+)"
        //     $pattern
        // );

        // for ($i = 0; $i < $params; $i++) { 
        //     $pattern = preg_replace(
        //         '/{'. $params[""] .'}/',    # Replace "{parameter}"
        //         '(?<${1}>'. $allowedParamChars . ')', # with "(?<parameter>[a-zA-Z0-9\_\-]+)"
        //         $pattern
        //     );
        // }

        foreach ($params as $key => $value) {
            
            $pattern = preg_replace(
                '/{'. $key .'}/',    # Replace "{parameter}"
                '(?< '. $key.'>'.$value . ')', # with "(?<parameter>[a-zA-Z0-9\_\-]+)"
                $pattern
            );
            
        }
        echo $pattern."\n";
        

        $pattern = preg_replace(
            '/{('. $allowedParamChars .')}/',    # Replace "{parameter}"
            '(?<${1}>'. $allowedParamChars . ')', # with "(?<parameter>[a-zA-Z0-9\_\-]+)"
            $pattern
        );

        // print_r($pattern);
        // echo " ";
    
        // Превращаем в полноценный regex
        $patternAsRegex = "@^" . $pattern . "$@D";
    
        return $patternAsRegex;
    }

    public function route(): string
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            /**
             * Берем относительный путь из строки браузера,
             * например, /news/1, где 1 - id
             */

            // $path = $_SERVER['REQUEST_URI'];
            // echo $path . "\n";

            $router = static::getInstance();

            // echo "PATH: ".$router->routes[$path]["mask"]."  ";

            /**
             * Ищем в путях переменные в фигурных скобках, например {id},
             * чтобы проверить эти переменные на соответствие параметрам
             */
            for ($i = 0; $i < count($router->routes); $i++) {
                $router->getRegex($router->routes[$i]["uri"], $router->routes[$i]["params"])."\n";

                // $string = $router->routes[$i]["uri"];
                // $pattern = '/\{.*\}/i';
                // preg_match($pattern, $string, $matches);
                // echo "MATCHES: ";
                // print_r($matches);
                // $replacement = '${1}1,$3';
                // echo preg_replace($pattern, $replacement, $string);

                // print_r($router->routes[$i]);
                // preg_match('/\{.*\}/', $router->routes[$i]["uri"], $matches, PREG_OFFSET_CAPTURE);
                // echo "MATCHES: ";
                // print_r($matches);
                // echo "  ";

                /**
                 * Ищем в параметрах найденную переменную и проверяем, соответствует
                 * ли она паттерну
                 */
                /**
                 * /user/123/456 и /user/{id}/{group}
                 * Берем из /user/{id}/{group} /user/ (всё до {id}), после чего
                 * ищем в /user/123/456 всё от /user/ до следующей /.
                 * Это будет значение id.
                 */
                // foreach ($router->routes[$i]["params"] as $key => $value) {
                //     // echo $router->routes[$i]["uri"]." ";
                //     // preg_match('/\{.*\}/', $router->routes[$i]["uri"], $matches, PREG_OFFSET_CAPTURE);
                //     // print_r($matches[0]);

                //     $pos = strripos($router->routes[$i]["uri"], $key);
                //     // $replacement = '${1}1,$3';
                //     echo "POS: " . $pos;
                //     if ($pos > 0) {
                //         echo $router->routes[$i]["uri"] . " ";
                //         $match = substr($router->routes[$i]["uri"], 0, $pos);
                //     }
                //     // echo preg_replace($pattern, $replacement, $router->routes[$i]["uri"]);
                // }
            }



            // $router->routes[$path]["mask"]();
            // use {};

            // TODO $_SERVER['REQUEST_URI'];

            $result = "null\n";
        } else {
            $result = "123\n";
        }

        return $result;
    }

    protected function __construct()
    {
    }
}
