<?php

namespace Libs\Http;

class Route
{
    protected string $userRoute;
    protected Prepare $prepare;
    protected bool $isFind = false; // Флаг костыль чтобы нельзя было задублировать контроллеры TODO

    //
    // Нужно будет переписать систему роутов, так чтобы, сначала скопить спиоск маршрутов в массиве, а потом
    // уже выполнять программу, сейчас реализация, очень раздутая (Комментарий пишу для себя) TODO
    //
    // Пример:
    // [
    //     "GET" => [
    //         "user/route"
    //     ]
    // ]
    //
    public function __construct()
    {
        $this->prepare = new Prepare();

        $userRoute = (string) ($_SERVER["REQUEST_URI"] ?? "");

        $this->userRoute = trim($userRoute, "/");
    }

    protected function compareRouteWithCurrentPathAndLoadController(string $systemRoute, string|callable $controller, string $functionName = "index"): void
    {
        //
        // Ставим костыль чтобы нельзя было подклюить несколько контроллеров TODO
        //
        if ($this->isFind) {
            return;
        }

        $systemRoute = trim($systemRoute, "/");

        $routeData = $this->prepare->contain($systemRoute, $this->userRoute);

        if (isset($routeData["routesIsMatch"])) {
            settype($routeData["paramsOfRoute"], "object");

            if (is_callable($controller)) {
                call_user_func($controller, $routeData["paramsOfRoute"]);

                $this->isFind = true;
            }

            if (is_string($controller)) {
                str_replace("/", "\\", $controller);
            }

            if (str_starts_with($controller, "App\\Controller\\")) {
                $controller = new $controller();

                $controller->$functionName($routeData["paramsOfRoute"]);

                $this->isFind = true;
            }
        }
    }

    public function get(string $systemRoute, string|callable $controller, string $functionName = "") {
        $this->compareRouteWithCurrentPathAndLoadController($systemRoute, $controller, $functionName);
    }
}