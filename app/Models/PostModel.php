<?php
require_once __DIR__ . "/../Core/Database.php";

class PostModel{
    
    //Conexão com o Banco de Dados;
    private $db;

    //Método Construtor (Conectar-se ao Banco de Dados)
    public function __construct()
    {
        $this->db = (new Database)->connect();
    }

    //GET * Posts
    public function getAllPosts()
    {
        try{

            //Consulta SQL para pegar todos os Posts
            $sql = "SELECT p.id p.message p.post_img u.username u.user_image
                    FROM posts p
                    INNER JOIN users u ON p.users_id = u.id
                    ORDER BY u.id DESC";
            
            // Preparando a consulta
            $stmt = $this->db->prepare($sql);

            // Executando a query e a retornando
            $stmt->execute();
            return [$stmt->fetchAll(PDO::FETCH_ASSOC), true];
        }
        catch (PDOException $e){
            return ["Erro " . $e->getMessage(), false];
        }
    }

    //By Post ID
    public function getPostByID($id)
    {
        try{

            //Consulta SQL para pegar todos os Posts
            $sql = "SELECT p.id p.message p.post_img u.username u.user_image
                    FROM posts p
                    INNER JOIN users u ON p.users_id = u.id
                    WHERE p.id = :id LIMIT 1";
            
            // Preparando a consulta
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            // Executando a query
            $stmt->execute();   

            //Resultado
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ? [$result, true] : [null, false];
        }
        catch (PDOException $e){
            return ["Erro " . $e->getMessage(), false];
        }
    }

    //By User ID
    public function getPostByUserID($userId)
    {
        try{

            //Consulta SQL para pegar todos os Posts
            $sql = "SELECT *
                    FROM posts
                    WHERE user_id = :userId
                    ORDER BY id DESC";
            
            // Preparando a consulta
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);

            // Executando a query
            $stmt->execute();   

            //Resultado
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result ? [$result, true] : [null, false];
        }
        catch (PDOException $e){
            return ["Erro " . $e->getMessage(), false];
        }
    }

    //CREATE
    public function createPost($message, $post_img = null, $user_id)
    {
        try{

      // Prepara a query SQL para criar um novo usuário
      $sql = "INSERT INTO posts (message, post_image, user_id) 
              VALUES (:message, :post_image, :user_id)";

      $stmt = $this->db->prepare($sql);

      // Configura os parâmetros
      $stmt->bindParam(":message", $message, PDO::PARAM_STR);
      $stmt->bindParam(":post_img", $postImg, PDO::PARAM_STR);
      $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
        
      // Executa a query e retorna uma mensagem
      return $stmt->execute() ? ["Post criado com sucesso!", true] : ["Erro ao criar Post.", false];

        }
        catch (PDOException $e){
            return ["Erro " . $e->getMessage(), false];
        }
    }

    //DELETE
    public function deletePost($post_id){
     try {
      $sql = "DELETE FROM posts WHERE id = :id";
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(":id", $post_id, PDO::PARAM_INT);

      return $stmt->execute()
        ? ["Post deletado com sucesso!", true]
        : ["Erro ao deletar post.", false];
    } catch (PDOException $e) {
      return ["Erro: " . $e->getMessage(), false];
    }
    }
}
?>