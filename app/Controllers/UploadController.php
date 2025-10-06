<?php
// Controlador de upload de arquivos para o endpoint "uploads"
require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/../Middlewares/AuthMiddleware.php';
require_once __DIR__ . "/../Views/JsonView.php";

class UploadController
{
  // Atributos privados da classe
  private $userModel;

  // Método construtor da classe
  public function __construct()
  {
    $this->userModel = new UserModel();
  }

  /**
   * Método de upload de foto de perfil.
   * Funciona tanto para criar uma nova foto quanto para atualizar a existente.
   */
  public function upload_pfp()
  {
    // ✅ Verifica token antes de qualquer ação
    $userId = AuthMiddleware::handle();

    // Obtém os dados do usuário pelo ID
    $result = $this->userModel->getUserById($userId);
    if (!$result[1]) {
      JsonView::render(["success" => false, "error" => $result[0]], 400);
      return;
    }
    $userData = $result[0];

    // Verifica se o arquivo foi enviado
    if (!isset($_FILES['file'])) {
      JsonView::render(["success" => false, "error" => "Nenhum arquivo enviado."], 400);
      return;
    }

    $file = $_FILES['file'];
    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    // Verifica se não houve erro no upload
    if ($fileError !== UPLOAD_ERR_OK) {
      JsonView::render(["success" => false, "error" => "Erro no upload: " . $fileError], 400);
      return;
    }

    // Validação de tipo e extensão do arquivo
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $fileMime = mime_content_type($fileTmp);
    $allowedMime = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($fileExt, $allowedExtensions) || !in_array($fileMime, $allowedMime)) {
      JsonView::render(["success" => false, "error" => "Arquivo inválido."], 400);
      return;
    }

    // Verifica tamanho máximo (5MB)
    $maxSize = 5 * 1024 * 1024;
    if ($fileSize > $maxSize) {
      JsonView::render(["success" => false, "error" => "Arquivo maior que 5MB."], 400);
      return;
    }

    // Define pasta de destino
    $dir = "user_images/";
    $targetDir = __DIR__ . "/../../public/uploads/" . $dir;
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    // Se o usuário já tem uma foto, deleta o arquivo antigo
    if (!empty($userData['user_image'])) {
      $oldFile = __DIR__ . "/../../public/uploads/user_images/" . basename($userData['user_image']);
      if (file_exists($oldFile)) {
        unlink($oldFile);
      }
    }

    // Gera um nome único para o novo arquivo
    $safeName = uniqid('pfp_', true) . '.' . $fileExt;

    // Move o arquivo para a pasta do servidor
    if (move_uploaded_file($fileTmp, $targetDir . $safeName)) {
      $fileUrl = "http://localhost/crud/uploads/user_images/" . $safeName;

      // Atualiza a URL da foto no banco
      $data = new stdClass;
      $data->userImg = $fileUrl;

      $updateResult = $this->userModel->updateUser($userData['id'], $data);

      if ($updateResult[1]) {
        JsonView::render(["success" => true, "url" => $fileUrl], 200);
      } else {
        JsonView::render(["success" => false, "error" => $updateResult[0]], 400);
      }
    } else {
      JsonView::render(["success" => false, "error" => "Erro ao salvar arquivo."], 500);
    }
  }
}
