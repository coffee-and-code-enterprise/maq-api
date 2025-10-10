<?php

// Função que pega o PUT e converte para o POST (um hack para o PHP)
if (!function_exists('parsePutMultipartFormData')) {
  function parsePutMultipartFormData()
  {
    // Pega o conteúdo cru da requisição
    $input = file_get_contents('php://input');
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    // Captura o boundary
    if (preg_match('/boundary=(.*)$/', $contentType, $matches)) {
      $boundary = $matches[1];
    } else {
      return [[], []];
    }

    $blocks = preg_split("/-+$boundary/", $input);
    array_pop($blocks); // remove o último elemento vazio

    $post = [];
    $files = [];

    foreach ($blocks as $block) {
      if (empty($block)) continue;

      // Campo simples
      if (preg_match('/name="([^"]+)"\r\n\r\n(.*)\r\n$/s', $block, $matches)) {
        $post[$matches[1]] = $matches[2];
      }

      // Arquivo
      elseif (preg_match('/name="([^"]+)"; filename="([^"]+)"\r\nContent-Type: ([^\r\n]+)\r\n\r\n(.*)\r\n$/s', $block, $matches)) {
        $name = $matches[1];
        $filename = $matches[2];
        $type = $matches[3];
        $content = $matches[4];

        $tmpPath = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($tmpPath, $content);

        $files[$name] = [
          'name' => $filename,
          'type' => $type,
          'tmp_name' => $tmpPath,
          'error' => 0,
          'size' => strlen($content),
        ];
      }
    }

    return [$post, $files];
  }
}
