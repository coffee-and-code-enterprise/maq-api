---

# 📘 Make A Question - API (PHP MVC)

A **Make A Question API** é a camada de backend da rede social **Make A Question**, uma plataforma que permite a publicação de perguntas, respostas e interações, inspirada no modelo do Reddit.
Construída em **PHP** com a arquitetura **MVC (Model-View-Controller)**, segue o padrão **RESTful**, garantindo organização, escalabilidade e facilidade de manutenção.

---

## 🚀 Funcionalidades

* **Gerenciamento de Usuários**

  * Cadastro e autenticação de contas.
  * Atualização de dados de perfil.
  * Autenticação com **JWT**. (planos futuros)

* **Publicação de Conteúdo**

  * Criar, editar e excluir **perguntas**.
  * Criar, editar e excluir **respostas** e **comentários**.

* **Interações**

  * Sistema de favoritar POST
  * Listagem de conteúdos mais votados ou mais recentes.

* **Organização**

  * Filtro por categorias.
  * Busca de perguntas por palavra-chave.

---

## 🛠️ Tecnologias Utilizadas

* **Linguagem:** PHP 8+
* **Banco de Dados:** MySQL
* **Servidor Local:** Apache (XAMPP)
* **Arquitetura:** MVC + RESTful
* **Autenticação:** JSON Web Token (JWT) - PLANOS FUTUROS
* **ORM:** PDO para comunicação com MySQL

---

## 📂 Estrutura de Pastas

```
/make-a-question-api
│── /app
│   ├── /Controllers   # Controladores (UserController, QuestionController, etc.)
│   ├── /Models        # Modelos (User.php, Question.php, Answer.php, etc.)
│   ├── /Views         # (Não utilizado para renderização; apenas para retorno JSON)
│   ├── /Core          # Classes centrais (Router.php, Database.php, Auth.php)
│   └── /Helpers       # Funções auxiliares (Validação, JWT, etc.)
│
│── /config
│   └── config.php     # Configurações do banco de dados e constantes
│
│── /public
│   └── index.php      # Ponto de entrada da aplicação
│
│── README             # Documentação
│── LICENSE            # Licença
```

---

## 📖 Endpoints Principais

### Usuários

* `POST /users/register` → Cadastrar usuário
* `POST /users/login` → Autenticar usuário
* `GET /users/{id}` → Obter perfil do usuário

---

## ⚙️ Configuração do Ambiente

1. Instale e ative o **XAMPP**.
2. Clone o repositório para a pasta `htdocs`:

   ```bash
   git clone https://github.com/seu-usuario/make-a-question-api.git
   ```
3. Configure o banco de dados no arquivo `config/config.php`.
4. Importe o script SQL disponível em `/database/make_a_question.sql`.
5. Acesse pelo navegador ou via ferramentas de API (Postman/Insomnia):

   ```
   http://localhost/make-a-question-api/public/
   ```

---

## 📌 Contribuição

1. Faça um **fork** do repositório.
2. Crie uma branch: `git checkout -b minha-feature`.
3. Faça commit: `git commit -m "Descrição da feature"`.
4. Envie: `git push origin minha-feature`.
5. Abra um **Pull Request**.

---

## 📄 Licença

Este projeto é licenciado sob a **MIT License**.
Consulte o arquivo [LICENSE](LICENSE) para mais informações.

---
