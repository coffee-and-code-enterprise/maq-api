<?php
/*
  Esse arquivo é o ponto de entrada da API,
  quando se faz a requisição HTTP, é neste
  arquivo que é realizada a abstração da API.
*/

require_once __DIR__ . "/../app/Core/Router.php";  // Importando o Router para poder chamá-lo no código

// Configurando o CORS
$allowedOrigins = [
  'http://localhost:5173',  // Endereço do servidor front-end
  // Se necessário, adicionarei mais domínios aqui
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
  // Se a origem do HTTP estiver permitida, ele continua o código
  header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}

// Métodos e Headers permitidos
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Se o tipo de método for 'OPTIONS' ele finaliza o código
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204);
  exit;
}

// Instancia a classe Router e chama a rota
$router = new Router();
$router->route($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);