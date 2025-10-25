<?php
require_once __DIR__ . '/../Helpers/JWT.php';
require_once __DIR__ . '/../Views/JsonView.php';

// Essa função é necessária para hosts que não utilizam APACHE
if (!function_exists('getallheaders')) {
  function getallheaders()
  {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
      if (substr($name, 0, 5) == 'HTTP_') {
        $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
        $headers[$headerName] = $value;
      }
    }
    return $headers;
  }
}

class AuthMiddleware
{
  public static function handle()
  {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
      JsonView::render(["success" => false, "error" => "Token não enviado"], 401);
      exit;
    }

    $token = str_replace("Bearer ", "", $headers['Authorization']);
    $payload = JWT::validate($token);

    // Verifica se contem um payload e se o mesmo contem um campo de ID
    if (!$payload || !$payload['id']) {
      JsonView::render(["success" => false, "error" => "Token inválido ou expirado"], 401);
      exit;
    }

    return $payload['id'];
  }
}
