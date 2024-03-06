<?php

namespace Controllers;

class Controller
{
    protected function view($fileName, $data = [])
    {
        $viewsPath = __DIR__ . '/../../resources/views/';

        $filePath = $viewsPath . $fileName;
        if (file_exists($filePath)) {
            extract($data);
            require __DIR__ . '/../../resources/views/layouts/header.php';
            require $filePath;
            require __DIR__ . '/../../resources/views/layouts/footer.php';
        } else {
            echo "View not found";
        }
    }
}