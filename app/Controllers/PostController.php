<?php
require_once __DIR__ . "/../Models/PostModel.php";
require_once __DIR__ . "/../Views/JsonView.php";
require_once __DIR__ . '/../Middlewares/AuthMiddleware.php';

// Classe que controla os Posts. Controlado entre o View e Model
class PostController{
    //Model que vai manipular o Banco de Dados
    private $postModel;

    //Cria a Model
    public function __construct()
    {
        $this->postModel = new PostModel();
    }

    
}

?>