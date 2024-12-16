<?php declare(strict_types=1);

require_once __DIR__ . '/../models/Leitura.php';

class LeituraController
{
    private Leitura $leituraModel;

    public function __construct(PDO $pdo)
    {
        $this->leituraModel = new Leitura($pdo);
    }

    // Listar todas as leituras
    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(["error" => "Método não permitido."]);
            return;
        }

        // Captura os parâmetros de data, se existirem
        $start_date = $_GET['start_date'] ?? null;
        $end_date = $_GET['end_date'] ?? null;

        try {
            $leituras = $this->leituraModel->getAll($start_date, $end_date);

            if (empty($leituras)) {
                http_response_code(404);
                echo json_encode(["message" => "Nenhuma leitura encontrada."]);
                return;
            }

            echo json_encode($leituras);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // Listar todas última leitura
    public function showLatest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(["error" => "Método não permitido."]);
            return;
        }

        try {
            $leitura = $this->leituraModel->getLatest();

            if (empty($leitura)) {
                http_response_code(404);
                echo json_encode(["message" => "Nenhuma leitura encontrada."]);
                return;
            }

            echo json_encode($leitura);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // Mostrar leitura específica por ID
    public function show(int $id): void
    {
        try {
            $leitura = $this->leituraModel->getById($id);

            if ($leitura === false) {
                http_response_code(404);
                echo json_encode(["message" => "Leitura não encontrada."]);
                return;
            }

            echo json_encode($leitura);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // Criar uma nova leitura
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["error" => "Método não permitido."]);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['id_estacao']) || empty($data['dados_json'])) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos."]);
            return;
        }

        try {
            // Passa os dados diretamente sem o token (o modelo buscará o token no cabeçalho)
            $id = $this->leituraModel->create($data);

            if ($id === false) {
                http_response_code(403);
                echo json_encode(["error" => "Token inválido."]);
                return;
            }

            http_response_code(201);
            echo json_encode(["message" => "Leitura criada com sucesso.", "id" => $id]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // Atualizar uma leitura
    public function update(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            http_response_code(405);
            echo json_encode(["error" => "Método não permitido."]);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['id_estacao']) || empty($data['dados_json'])) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos."]);
            return;
        }

        try {
            // Passa os dados diretamente sem o token (o modelo buscará o token no cabeçalho)
            $success = $this->leituraModel->update($id, $data);

            if ($success) {
                echo json_encode(["message" => "Leitura atualizada com sucesso."]);
            } else {
                http_response_code(403);
                echo json_encode(["error" => "Token inválido ou leitura não encontrada."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // Excluir uma leitura
    public function delete(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode(["error" => "Método não permitido."]);
            return;
        }

        try {
            // O modelo buscará o token no cabeçalho
            $success = $this->leituraModel->delete($id);

            if ($success) {
                echo json_encode(["message" => "Leitura excluída com sucesso."]);
            } else {
                http_response_code(403);
                echo json_encode(["error" => "Token inválido ou leitura não encontrada."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}
