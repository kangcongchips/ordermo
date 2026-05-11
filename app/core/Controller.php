<?php

class Controller
{
    protected function model(string $model)
    {
        return new $model();
    }

    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        $viewPath   = __DIR__ . '/../views/' . $view . '.php';
        $layoutPath = __DIR__ . '/../views/layouts/' . $layout . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(404);
            echo "View not found: {$view}";
            return;
        }

        extract($data);

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }
}
