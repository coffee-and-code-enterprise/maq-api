<?php
require_once __DIR__ . "/../Models/PostModel.php";
require_once __DIR__ . "/../Views/JsonView.php";
require_once __DIR__ . '/../Middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../Helpers/FileUploader.php';

class PostController
{
    //GET Methods

    //Model que acessara o Banco de Dados (Privado);
    private $postModel;
    private $fileUploader;

    public function __construct()
    {
        $this->postModel = new PostModel();
        $this->fileUploader = new FileUploader();
    }

    // Retornando todos os Posts
    public function index()
    {
        $result = $this->postModel->getAllPosts();
        return JsonView::render(["post" => $result[0], "success" => $result[1]], 200);
    }

    // Retornando um Post pelo ID do Post
    public function show($id)
    {
        $result = $this->postModel->getPostByID($id);
        if ($result[1]) {
            return JsonView::render(["post" => $result[0], "success" => true], 200);
        } else {
            return JsonView::render(["error" => $result[0], "success" => false], 400);
        }
    }

    // Retornando os Posts pelo Id do usuário
    public function getByUser($user_id)
    {
        $result = $this->postModel->getPostByUserID($user_id);
        return JsonView::render(["post" => $result[0], "success" => $result[1]], 200);
    }

    //POST Methods
    // Criar um Post
    public function create()
    {
        // Garante que o usuário está autenticado e Pega seu ID salvo na sessão
        $user_id = AuthMiddleware::handle();

        // Suporte tanto para JSON quanto para FormData
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true);
            $message = $input['message'] ?? '';
            $file = null; // Não há suporte para upload de arquivo via JSON puro
        } else {
            $message = $_POST['message'] ?? '';
            $file = $_FILES['image'] ?? null;
        }

        // Confere se o post tem mensagem
        if (!isset($message) || empty(trim($message))) {
            return JsonView::render(["error" => "Mensagem Obrigatória", "success" => false], 400);
        }

        // Processamento do Upload de Arquivo (Se houver)
        $post_img = null;

        // Verifica se há um arquivo para upload
        if (isset($file) && $file['error'] !== UPLOAD_ERR_NO_FILE) {

            // Define o subdiretório. Ex: 'posts/' ou 'posts/' . $user_id
            $subDir = 'posts/';

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
            $post_img = $fileUrl;
        }

        // Criação do Post no Banco de Dados
        $result = $this->postModel->createPost($message, $user_id, $post_img);

        // 5. Resposta
        return JsonView::render(["message" => $result[0], "success" => $result[1]], $result[1] ? 201 : 400);
    }

    //DELETE Methods

    //Deletar um Post
    public function delete($id)
    {
        // Verifica se o Usuário esta conectado e pega seu ID
        $user_id = AuthMiddleware::handle();

        // Verifica se o Post existe
        $postResult = $this->postModel->getPostByID($id);
        if (!$postResult[1]) {
            return JsonView::render(["error" => "Post não encontrado", "success" => false], 404);
        }
        $post = $postResult[0];

        // Verifica se o Post pertence ao usuário autenticado
        if ($user_id !== $post["user_id"]) {
            return JsonView::render(["error" => "Acesso Negado", "success" => false], 403);
        }

        if (!empty($post["post_img"])) {
            // A classe FileUploader.php espera o caminho relativo à pasta /public/uploads/
            // Precisamos extrair esse caminho da URL completa.
            // Ex: "http://localhost/maq-api/public/uploads/posts/file_650b91d6438183.18129790.png"
            // Path esperado: "posts/file_650b91d6438183.18129790.png"

            // Simplificação: assume que a parte de interesse é após /public/uploads/
            $uploadPathSegment = '/public/uploads/';
            $pathParts = explode($uploadPathSegment, $post["post_img"]);

            if (count($pathParts) > 1) {
                $filePath = $pathParts[1];
                $this->fileUploader->deleteFile($filePath);
                // A falha na exclusão do arquivo não impede a exclusão do post no DB,
                // mas seria ideal logar esse erro.
            }
        }

        $result = $this->postModel->deletePost($id);
        return JsonView::render(["message" => $result[0], "success" => $result[1]], 200);
    }
}
