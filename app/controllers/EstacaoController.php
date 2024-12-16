<?php declare(strict_types=1);

require_once __DIR__ . '/../models/Estacao.php';

class EstacaoController
{
    private Estacao $estacaoModel;

    public function __construct(PDO $pdo)
    {
        $this->estacaoModel = new Estacao($pdo);
    }

    // Listar todas as estações
    public function index(): void
    {
        try {
            $estacoes = $this->estacaoModel->getAll();

            if ($estacoes === false) {
                echo json_encode(["message" => "Nenhuma estação encontrada."]);
                return;
            }

            echo json_encode($estacoes);
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // Mostrar uma estação específica pelo ID
    public function show(int $id): void
    {
        try {
            $estacao = $this->estacaoModel->getById($id);

            if ($estacao === false) {
                echo json_encode(["message" => "Estação não encontrada."]);
                return;
            }

            echo json_encode($estacao);
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // Criar uma nova estação
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(["error" => "Método não permitido."]);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['endereco']) || empty($data['latitude']) || empty($data['longitude'])) {
            echo json_encode(["error" => "Dados incompletos."]);
            return;
        }

        try {
            $id = $this->estacaoModel->create([
                'endereco' => $data['endereco'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
            ]);

            $estacao = $this->estacaoModel->getById($id);

            echo json_encode(["message" => "Estação criada com sucesso.", "estacao" => $estacao]);
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // Atualizar uma estação
    public function update(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            echo json_encode(["error" => "Método não permitido."]);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['endereco']) || empty($data['latitude']) || empty($data['longitude'])) {
            echo json_encode(["error" => "Dados incompletos."]);
            return;
        }

        try {
            $success = $this->estacaoModel->update($id, [
                'endereco' => $data['endereco'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
            ]);

            if ($success) {
                echo json_encode(["message" => "Estação atualizada com sucesso."]);
            } else {
                echo json_encode(["message" => "Nenhuma alteração realizada."]);
            }
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // Deletar uma estação
    public function delete(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            echo json_encode(["error" => "Método não permitido."]);
            return;
        }

        try {
            $success = $this->estacaoModel->delete($id);

            if ($success) {
                echo json_encode(["message" => "Estação excluída com sucesso."]);
            } else {
                echo json_encode(["message" => "Estação não encontrada ou não foi possível excluir."]);
            }
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // Buscar uma estação pelo token
    public function showByToken(string $token): void
    {
        try {
            $estacao = $this->estacaoModel->getByToken($token);

            if ($estacao === false) {
                echo json_encode(["message" => "Estação não encontrada."]);
                return;
            }

            echo json_encode($estacao);
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}
