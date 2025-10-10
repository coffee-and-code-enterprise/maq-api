<?php
require_once __DIR__ . "/../Models/UserModel.php";
require_once __DIR__ . "/../Views/JsonView.php";
require_once __DIR__ . '/../Middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../Helpers/FileUploader.php';
require_once __DIR__ . '/../Helpers/RequestHelper.php';

// Classe que controla o usuário. É responsável por fazer a ponte entre o model e a view
class UserController
{
  // Atributos privados da classe
  private $userModel;
  private $fileUploader;

  // Método construtor. Cria o modelo do usuário
  public function __construct()
  {
    $this->userModel = new UserModel();
    $this->fileUploader = new FileUploader();
  }

  // Método que retorna todos os usuários da tabela
  public function index()
  {
    $result = $this->userModel->getAllUsers();
    return JsonView::render([$result], 200);
  }

  // Método que autentica o usuário
  public function auth()
  {
    $userId = AuthMiddleware::handle();

    return JsonView::render([
      "message" => "Usuário autenticado!",
      "userId"  => $userId,
      "success" => true
    ], 200);
  }

  // Método POST que cria usuário
  public function create($data)
  {
    if (!$data->username || !$data->email || !$data->password) {
      JsonView::render(["success" => false, "error" => "Campos obrigatórios faltando"], 400);
      return;
    }

    $result = $this->userModel->createUser(
      $data->username,
      $data->email,
      $data->password,
      $data->phone ?? null,
      $data->userImg ?? null
    );

    JsonView::render(["message" => $result[0], "success" => $result[1]]);
  }

  // Método que retorna o usuário pelo Id
  public function show($id)
  {
    $result = $this->userModel->getUserById($id);
    if ($result[1]) {
      $userData = $result[0];
      return JsonView::render([
        'id'          => $userData['id'],
        'username'    => $userData['username'],
        'email'       => $userData['email'],
        'phone'       => $userData['phone'],
        'user_image' => $userData['user_image']
      ]);
    } else {
      return JsonView::render(["error" => $result[0], "success" => $result[1]], 400);
    }
  }

  // Método que atualiza um usuário através do ID
  public function update($id)
  {
    // Se for PUT, faz o parse manual do FormData
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
      [$post, $files] = parsePutMultipartFormData();
      $_POST = $post;
      $_FILES = $files;
    }

    $data = json_decode(json_encode($_POST)) ?? null;
    $file = $_FILES['image'] ?? null;

    // Processamento do Upload de Arquivo (Se houver)
    $user_image = null;

    // Verifica se há um arquivo para upload
    if (isset($file) && $file['error'] !== UPLOAD_ERR_NO_FILE) {

      // Define o subdiretório. Ex: 'posts/' ou 'posts/' . $user_id
      $subDir = 'user_images/';

      [$fileUrl, $errors] = $this->fileUploader->upload($file, $subDir);

      if ($fileUrl === null) {
        // Se o upload falhar, retorna o erro
        return JsonView::render([
          "error" => "Falha no upload da imagem",
          "details" => $errors,
          "success" => false
        ], 400);
      }
      // Se o upload for bem-sucedido, salva a URL da imagem
      $user_image = $fileUrl;
    }

    // Aguarda o resultado da operação de update
    $result = $this->userModel->updateUser($id, $data, $user_image);

    // Retorna o resultado da operação
    JsonView::render(["message" => $result[0], "success" => $result[1]], 200);
    return;
  }

  // Método que deleta um usuário do banco de dados
  public function delete($id)
  {
    $result = $this->userModel->deleteUser($id);

    if ($result[1] === true) {
      $code = 200;
    } else {
      $code = 500;
    }
    
    JsonView::render(["message" => $result[0], "success" => $result[1]], $code);
  }
}
