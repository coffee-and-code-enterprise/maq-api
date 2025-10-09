<?php

class FileUploader
{
  private $allowedExtensions;
  private $allowedMimes;
  private $maxSize;
  private $uploadDir;
  private $errors = [];

  public function __construct()
  {
    $this->allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $this->allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
    $this->maxSize = 5 * 1024 * 1024; // 5MB
    $this->uploadDir = __DIR__ . "/../../public/uploads/";
  }

  public function upload($file, $subDir)
  {
    if (!$this->validateFile($file)) {
      return [null, $this->errors];
    }

    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Criar diretório se não existir
    $targetDir = $this->uploadDir . $subDir;
    if (!is_dir($targetDir)) {
      mkdir($targetDir, 0777, true);
    }

    // Gerar nome único
    $safeName = uniqid('file_', true) . '.' . $fileExt;
    $targetPath = $targetDir . $safeName;

    // Mover arquivo
    if (move_uploaded_file($fileTmp, $targetPath)) {
      $fileUrl = "http://localhost/maq-api/public/uploads/" . $subDir . $safeName;
      return [$fileUrl, null];
    }

    return [null, ["Erro ao mover arquivo"]];
  }

  private function validateFile($file)
  {
    // Verificar se arquivo foi enviado
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
      $this->errors[] = "Erro no upload do arquivo";
      return false;
    }

    // Verificar tamanho
    if ($file['size'] > $this->maxSize) {
      $this->errors[] = "Arquivo maior que 5MB";
      return false;
    }

    // Verificar extensão
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExt, $this->allowedExtensions)) {
      $this->errors[] = "Extensão não permitida";
      return false;
    }

    // Verificar MIME type usando finfo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $this->allowedMimes)) {
      $this->errors[] = "Tipo de arquivo não permitido";
      return false;
    }

    return true;
  }

  public function deleteFile($filePath)
  {
    $fullPath = __DIR__ . "/../../public/uploads/" . $filePath;
    if (file_exists($fullPath)) {
      return unlink($fullPath);
    }
    return false;
  }
}
