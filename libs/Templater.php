<?php

namespace libs;

class Templater
{
    public static function view(string $pathToTemplate, $variables)
    { // Метод для подлючения шаблонов сайта
        settype($variables, "object");

        $baseFilePath = "./views/$pathToTemplate.phtml";

        if (!file_exists($baseFilePath) && !is_readable($baseFilePath)) { // Проверяем файл на чтение
            throw new \Exception("Не возможно прочитить файл по пути: " . $baseFilePath);
        }

        try {
            $render = function($variables) use ($baseFilePath) {
                require $baseFilePath;
            };

            $render($variables);
        } catch (\Throwable $exception) {
            throw new \Exception("Ошибка подключения шаблона: " . $exception);
        }
    }
}