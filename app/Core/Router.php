<?php
require_once __DIR__ . "/../Controllers/UserController.php";
require_once __DIR__ . "/../Controllers/LoginController.php";
require_once __DIR__ . "/../Controllers/UploadController.php";

// Classe de controle de rota do sistema MVC
class Router
{
  // Atributos privados para os controllers
  private $userController;
  private $loginController;
  private $uploadController;

  // Método construtor que instância os controllers
  public function __construct()
  {
    $this->userController = new UserController();
    $this->loginController = new LoginController();
    $this->uploadController = new UploadController();
  }

  // Método público que retorna um código com base na rota inserida pelo cliente
  public function route($uri, $method)
  {
    // Remove query string da URI, se houver
    $uri = parse_url($uri, PHP_URL_PATH);

    // Separa as partes da uri por "/"
    $parts = explode("/", trim($uri, "/"));

    // Se a primeira parte for "maq-api/public", remove ela antes de continuar o código
    if ($parts[0] == 'maq-api') {
      array_shift($parts);
    }
    if ($parts[0] == 'public') {
      array_shift($parts);
    }

    // Obtendo a data do cliente
    $data = json_decode(file_get_contents("php://input"));

    // AUTENTIFICAÇÃO
    if ($parts[0] == "auth") {
      $action = $parts[1] ?? null;

      switch ($method) {
        case "POST": // Gera o token para o usuário
          $this->loginController->login($data);
          break;

        case "GET": // Valida o token e retorna os dados do usuário
          $this->userController->auth();
          break;

        default:
          http_response_code(405);
          echo json_encode(["success" => false, "error" => "Método não permitido"]);
          break;
      }
    }

    // ARQUIVOS
    if ($parts[0] == "upload") {
      $action = $parts[1] ?? null;

      switch ($method) {
        case "POST": // Adiciona o arquivo no servidor
          $this->uploadController->upload_pfp();
          break;

        default:
          http_response_code(405);
          echo json_encode(["success" => false, "error" => "Método não permitido"]);
          break;
      }
    }

    // CRUD RESTful
    if ($parts[0] === "users") {
      $id = $parts[1] ?? null;  // se tiver ID na rota

      switch ($method) {
        case "POST": // Criar usuário

          $this->userController->create($data);
          return;

        case "GET":
          if ($id) {
            // GET /users/{id} -> mostra um usuário
            $this->userController->show($id);
          } else {
            // GET /users -> lista usuários
            $this->userController->index();
          }
          return;

        case "PUT":
        case "PATCH":
          if ($id) {
            $this->userController->update($id, $data);
          } else {
            http_response_code(400);
            echo json_encode(["error" => "ID obrigatório para atualização"]);
          }
          return;

        case "DELETE":
          if ($id) {
            $this->userController->delete($id);
          } else {
            http_response_code(400);
            echo json_encode(["error" => "ID obrigatório para exclusão"]);
          }
          return;

        default:
          http_response_code(405);
          echo json_encode(["error" => "Método não permitido"]);
          return;
      }
    }
  }
}
