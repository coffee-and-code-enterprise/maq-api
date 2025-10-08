<?php 
require_once __DIR__ . "/../Models/PostModel.php";
require_once __DIR__ . "/../Views/JsonView.php";
require_once __DIR__ . '/../Middlewares/AuthMiddleware.php';

class PostController
{   
    //GET Methods

    //Model que acessara o Banco de Dados (Privado);
    private $postModel;

    public function __construct()
    {
        $this->postModel = new PostModel();
    }

    // Retornando todos os Posts
    public function index(){
        $result = $this->postModel->getAllPosts();
        return JsonView::render([["post" => $result[0], "success" => $result[1]], 200]);
    }

    // Retornando um Post pelo ID do Post
    public function show($id){
        $result = $this->postModel->getPostByID($id);
        if($result[1]){
            return JsonView::render(["post" => $result[0], "success" => true], 200);
        } else{
            return JsonView::render(["error" => $result[0], "success" => false], 400);
        }
    }  

    // Retornando os Posts pelo Id do usuário
    public function getByUser($user_id){
        $result = $this->postModel->getPostByUserID($user_id);
        JsonView::render(["post" => $result[0], "success" => $result[1]], 200);
    }

    //POST Methods

    // Criar um Post
    public function create($data){

        //Garante que o usuário está autenticado e Pega seu ID salvo na sessão
        $user_id = AuthMiddleware::handle();

        //Confere se o post tem menssagem
        if(!$data->message){
            return JsonView::render(["error" => "Menssagem Obrigatória", "success" => false], 400);
        }

        // Se Data => post_img existir e não for nula pega ela, caso contrário, pega null;
        $post_img = $data->post_img ?? null;

        $result = $this->postModel->createPost($data->message, $user_id, $post_img);
        
        return JsonView::render([["message" => $result[0], "sucess" => $result[1]], $result[1] ? 201 : 400]);
    }

    //DELETE Methods

    //Deletar um Post
    public function delete($id){

        //Verifica se o Usuário esta conectado e pega seu ID
        $user_id = AuthMiddleware::handle();

        //Verifica se o Post existe e é do Usuário antes de Deletar
        $post = $this->postModel->getPostByID($id);
        if(!$post[1]) return JsonView::render(["error" => "Post não encontrado", "success" => false], 404);
        if($user_id !== $post[0]["user_id"]) return JsonView::render(["error" => "Acesso Negado", "success" => false], 403);

        $result = $this->postModel->deletePost($id);
        return JsonView::render(["message" => $result[0], "success" => $result[1]], 200);
    }
}
?>