<?php
declare(strict_types=1);

// Para debugar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Headers padrão de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
header('X-XSS-Protection: 1; mode=block');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\'; style-src \'self\'');
header('Content-Security-Policy: upgrade-insecure-requests');
header('X-Permitted-Cross-Domain-Policies: none');
header('X-Robots-Tag: noindex, nofollow');

// Carregar bibliotecas
require_once __DIR__ . '/../libs/php_router/router.php';
require_once __DIR__ . '/../libs/dot_env/DotEnv.php';

// Carregar controllers
require_once __DIR__ . '/../app/controllers/EstacaoController.php';
require_once __DIR__ . '/../app/controllers/LeituraController.php';

// Conexão com o banco de dados
(new DotEnv(__DIR__ . '/../.env'))->load();
$pdo = new PDO(
    "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

// Instancia os controllers
$estacaoController = new EstacaoController($pdo);
$leituraController = new LeituraController($pdo);

// Rotas
try {
    // Rota para a Dashboard
    get('/', function () {
        header('Content-Type: text/html; charset=utf-8');
        require_once __DIR__ . '/../app/views/dashboard.php';
    });

    // Rotas de API para Estações
    get('/api/estacoes', function () use ($estacaoController) {
        header('Content-Type: application/json; charset=utf-8');
        $estacaoController->index();
    });
    get('/api/estacoes/$id', function ($id) use ($estacaoController) {
        header('Content-Type: application/json; charset=utf-8');
        $estacaoController->show((int) $id);
    });

    // Rotas de API para Leituras
    get('/api/leituras', function () use ($leituraController) {
        header('Content-Type: application/json; charset=utf-8');
        $leituraController->index();
    });
    get('/api/leituras/latest', function () use ($leituraController) {
        header('Content-Type: application/json; charset=utf-8');
        $leituraController->showLatest();
    });
    get('/api/leituras/$id', function ($id) use ($leituraController) {
        header('Content-Type: application/json; charset=utf-8');
        $leituraController->show((int) $id);
    });
    post('/api/leituras', function () use ($leituraController) {
        header('Content-Type: application/json; charset=utf-8');
        $leituraController->create();
    });
    put('/api/leituras/$id', function ($id) use ($leituraController) {
        header('Content-Type: application/json; charset=utf-8');
        $leituraController->update((int) $id);
    });
    delete('/api/leituras/$id', function ($id) use ($leituraController) {
        header('Content-Type: application/json; charset=utf-8');
        $leituraController->delete((int) $id);
    });

    any('/404', function () {
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

        if ($isAjax) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(["error" => "AJAX route not found"]);
        } else {
            http_response_code(404);
            require_once __DIR__ . '/../app/views/404.php';
        }
    });
} catch (Throwable $e) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode(["error" => "Ocorreu um erro inesperado.", "details" => $e->getMessage()]);
}
