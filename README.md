
# SustentAr - Monitoramento da Qualidade do Ar 🌱

Este repositório contém o código-fonte do projeto **SustentAr**, um sistema web integrado com sensores Arduino para monitorar em tempo real a qualidade do ar e a temperatura. O objetivo do projeto é auxiliar na redução da poluição atmosférica, subsidiar políticas públicas e promover a conscientização ambiental em **São Lourenço do Oeste**.


## 📋 Funcionalidades

- **Monitoramento em tempo real**:
  - Temperatura, umidade, CO (Monóxido de Carbono), partículas 2.5 nm e 1 nm.
- **Gestão de Estações**:
  - CRUD (Criar, Listar, Atualizar, Deletar) de estações de monitoramento.
- **Gestão de Leituras**:
  - CRUD de leituras capturadas pelos sensores.
  - Filtros por data para análises históricas.
- **Segurança**:
  - Autenticação via tokens para estações.
  - Cabeçalhos HTTP de proteção contra ataques comuns (XSS, CSRF, Clickjacking, etc.).
- **Relatórios**:
  - Exportação dos dados para CSV.

---

## 🏗️ Estrutura do Projeto

### Arquivos Principais

1. **`index.php`**  
   - Arquivo de entrada principal do sistema. Configura as rotas, conecta ao banco de dados e inicializa os controladores.

2. **`Estacao.php`**  
   - Modelo para gerenciamento das estações de monitoramento no banco de dados.

3. **`Leitura.php`**  
   - Modelo para gerenciamento das leituras capturadas pelos sensores no banco de dados.

4. **`EstacaoController.php`**  
   - Controlador responsável pelas operações relacionadas às estações.

5. **`LeituraController.php`**  
   - Controlador responsável pelas operações relacionadas às leituras.

6. **`creates.sql`**  
   - Script para a criação das tabelas MySQL.

7. **`.env_example`**  
   - Modelo de declaração das variáveis.
  
## 🚀 Como Executar o Projeto

### Pré-requisitos

- **PHP 8.0+**
- **MySQL** para o banco de dados.
- Servidor web como **Apache** ou **Nginx**.

### Instalação

1. Clone o repositório.    
23.  Configure o arquivo `.env` com as credenciais do banco de dados:
    
    ```
    DB_HOST=localhost
    DB_NAME=sustentar
    DB_USER=seu_usuario
    DB_PASS=sua_senha
    
    ```
    
3.  Importe o arquivo SQL com a estrutura do banco de dados.
    
4.  Inicie o servidor local.

## 📖 Rotas da API

### Estações

-   `GET /api/estacoes`  
    Lista todas as estações.
    
-   `GET /api/estacoes/{id}`  
    Exibe uma estação específica pelo ID.
        

### Leituras

-   `GET /api/leituras`  
    Lista todas as leituras (com suporte a filtros por data).  
    **Exemplo de uso**:  
    `/api/leituras?start_date=2023-01-01&end_date=2023-01-31`
    
-   `GET /api/leituras/latest`  
    Retorna a última leitura registrada.
    
-   `GET /api/leituras/{id}`  
    Exibe uma leitura específica pelo ID.
    
-   `POST /api/leituras`  
    Cria uma nova leitura.  
    
-   `PUT /api/leituras/{id}`  
    Atualiza uma leitura existente.
    
-   `DELETE /api/leituras/{id}`  
    Deleta uma leitura pelo ID.
    

## 🔒 Segurança

1.  **Autenticação via Token**  
    As estações possuem tokens únicos (`api_token`) para realizar operações seguras.
    
2.  **Cabeçalhos de Proteção**  
    Implementação de cabeçalhos HTTP para evitar ataques comuns (XSS, CSRF, Clickjacking).
    
3.  **Validação CSRF**  
    Funções específicas para proteção contra ataques CSRF.

## 📂 Estrutura de Pastas


```
.
├── app/
│   ├── controllers/
│   │   ├── EstacaoController.php
│   │   └── LeituraController.php
│   ├── models/
│   │   ├── Estacao.php
│   │   └── Leitura.php
│   └── views/
│       ├── dashboard.php
│       └── 404.php
├── libs/
│   ├── php_router/
│   │   └── router.php
│   └── dot_env/
│       └── DotEnv.php
├── public/
│   └── index.php
└── README.md
└── creates.sql

```

----------

## 🛠 Tecnologias Utilizadas

-   **PHP 8.0+**
-   **MySQL**
-   **jQuery**
-  **Bootstrap**
-   **HTML/CSS**
-   **JavaScript**

----------

## 🧑‍💻 Contribuindo

Contribuições são bem-vindas! Entre em contato pelo link: <sustentar.app.br>.

## 📜 Licença

Este projeto está licenciado sob a **MIT License**. Consulte o arquivo `LICENSE` para mais informações.


## 🌟 Agradecimentos

Agradeço a todos os professores e colegas do Instituto Federal de Santa Catarina - IFSC que tornaram o projeto possível.
