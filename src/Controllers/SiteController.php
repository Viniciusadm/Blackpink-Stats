<?php

namespace Controllers;

class SiteController extends Controller
{
    public function home()
    {
        $message = "Bom dia";

        $this->view('home.php', ['message' => $message]);
    }

    public function notFound()
    {
        $this->view('notFound.php');
    }
}