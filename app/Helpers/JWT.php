<?php
/*
  O JWT (JSON Web Token), é responsável por
  gerar e validar tokens.
*/

class JWT
{
  // Recupera a chave do token a partir do config (que por sua vez usa env)
  private static function getTokenKey()
  {
    $config = require __DIR__ . '/../../config/config.php';
    return $config['tokenKey'] ?? null;
  }

  // Codificação Base64 URL Safe
  private static function base64UrlEncode($data)
  {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }

  // Decodificação Base64 URL Safe
  private static function base64UrlDecode($data)
  {
    return base64_decode(strtr($data, '-_', '+/'));
  }

  // Gerar token
  public static function generate($payload, $exp = 3600)
  {
    $header = json_encode(["alg" => "HS256", "typ" => "JWT"]);

    // adiciona expiração
    $payload['exp'] = time() + $exp;

    $base64Header = self::base64UrlEncode($header);
    $base64Payload = self::base64UrlEncode(json_encode($payload));

    $tokenKey = self::getTokenKey();
    $signature = hash_hmac('sha256', "$base64Header.$base64Payload", $tokenKey, true);
    $base64Signature = self::base64UrlEncode($signature);

    return "$base64Header.$base64Payload.$base64Signature";
  }

  // Validar token
  public static function validate($token)
  {
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
      return false;
    }

    list($base64Header, $base64Payload, $base64Signature) = $parts;

    $tokenKey = self::getTokenKey();
    // Recalcular assinatura
    $expectedSignature = self::base64UrlEncode(
      hash_hmac('sha256', "$base64Header.$base64Payload", $tokenKey, true)
    );

    if (!hash_equals($expectedSignature, $base64Signature)) {
      return false; // assinatura inválida
    }

    // Decodificar payload
    $payload = json_decode(self::base64UrlDecode($base64Payload), true);

    // Verificar expiração
    if (isset($payload['exp']) && $payload['exp'] < time()) {
      return false; // expirado
    }

    return $payload; // retorna os dados do usuário
  }
}
