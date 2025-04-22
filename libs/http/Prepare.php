<?php

namespace libs\http;

class Prepare
{
    const patternForFindRouteParams = "/{(.*)}/";

    public function prepare(string $path): array
    {
        $path = parse_url($path, PHP_URL_PATH);

        if (!is_string($path)) {
            $path = "";
        }

        $paths = explode("/", $path);

        return is_array($paths) ? $paths : [];
    }

    public function contain(string $haystack, string $needle): array
    {
        $routeData = [];

        $routeContainsParams = (bool) preg_match_all(self::patternForFindRouteParams, $haystack);

        // Если в заданном маршруте отсутствуют «Параметры», то сравниваем маршруты как строки
        if (!$routeContainsParams && $haystack === $needle) {
            $routeData["routesIsMatch"] = true;
        }

        $haystack = $this->prepare($haystack);
        $needle   = $this->prepare($needle);

        if ($routeContainsParams && count($haystack) === count($needle)) {
            $routeData = $this->compareRouteWithParams($haystack, $needle);
        }

        return $routeData;
    }

    protected function compareRouteWithParams(array $haystack, array $needle): array {
        $routeData = [
            "routesIsMatch" => true,
            "paramsOfRoute" => []
        ];

        for ($i = 0; $i < count($needle); ++$i) {
            if (preg_match(self::patternForFindRouteParams, $haystack[$i], $matches)) {
                if (isset($matches[1])) {
                    $routeData["paramsOfRoute"][$matches[1]] = $needle[$i];

                    continue;
                }
            }

            if (!count($matches) && $haystack[$i] !== $needle[$i]) {
                $routeData["routesIsMatch"] = false;
                break;
            }
        }

        return $routeData;
    }
}