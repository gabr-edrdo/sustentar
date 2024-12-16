<?php declare(strict_types=1);

class Leitura
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Obter todas as leituras (ordenadas por data de criação)
    public function getAll(?string $start_date = null, ?string $end_date = null): array|bool
    {
        try {
            $query = "SELECT * FROM leituras";

            if ($start_date && $end_date) {
                $query .= " WHERE created_at BETWEEN :start_date AND :end_date";
            }

            $query .= " ORDER BY created_at DESC";

            $stmt = $this->pdo->prepare($query);

            if ($start_date && $end_date) {
                $stmt->bindParam(':start_date', $start_date);
                $stmt->bindParam(':end_date', $end_date);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar leituras: " . $e->getMessage());
        }
    }

    // Obter última leitura
    public function getLatest(): array|bool
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM leituras ORDER BY created_at DESC LIMIT 1");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar leitura: " . $e->getMessage());
        }
    }

    // Obter uma leitura específica por ID
    public function getById(int $id): array|bool
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM leituras WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar leitura: " . $e->getMessage());
        }
    }

    // Criar uma nova leitura
    public function create(array $dados): int|false
    {
        // Validar o token da estação
        if (!$this->validateToken($dados['id_estacao'])) {
            return false;
        }

        if (!$this->isValidJson($dados['dados_json'])) {
            throw new Exception("Formato inválido no campo dados_json.");
        }

        $sql = "INSERT INTO leituras (id_estacao, dados_json, created_at) 
                VALUES (:id_estacao, :dados_json, NOW())";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_estacao' => $dados['id_estacao'],
                ':dados_json' => $dados['dados_json']
            ]);

            return (int)$this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erro ao inserir leitura: " . $e->getMessage());
        }
    }

    // Atualizar uma leitura existente
    public function update(int $id, array $dados): bool
    {
        // Validar o token da estação
        if (!$this->validateToken($dados['id_estacao'])) {
            return false;
        }

        if (!$this->isValidJson($dados['dados_json'])) {
            throw new Exception("Formato inválido no campo dados_json.");
        }

        $sql = "UPDATE leituras
                SET id_estacao = :id_estacao, 
                    dados_json = :dados_json,
                    updated_at = NOW()
                WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':id_estacao' => $dados['id_estacao'],
                ':dados_json' => $dados['dados_json']
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar leitura: " . $e->getMessage());
        }
    }

    // Excluir uma leitura
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT id_estacao FROM leituras WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $leitura = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$leitura) {
            throw new Exception("Leitura não encontrada.");
        }

        // Validar o token da estação
        if (!$this->validateToken($leitura['id_estacao'])) {
            return false;
        }

        try {
            $stmt = $this->pdo->prepare("DELETE FROM leituras WHERE id = :id");
            $stmt->execute([':id' => $id]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erro ao excluir leitura: " . $e->getMessage());
        }
    }

    // Validar token da estação
    private function validateToken(int $id_estacao): bool
    {
        // Buscar o token no cabeçalho
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';

        // Remover o prefixo "Bearer " se estiver presente
        $token = str_starts_with($token, 'Bearer ') ? substr($token, 7) : $token;

        // Validar o token no banco de dados
        $stmt = $this->pdo->prepare("SELECT api_token FROM estacoes WHERE id = ?");
        $stmt->execute([$id_estacao]);
        $estacao = $stmt->fetch(PDO::FETCH_ASSOC);

        return $estacao && $estacao['api_token'] === $token;
    }

    // Verificar se o campo dados_json está no formato JSON válido
    private function isValidJson(string $data): bool
    {
        json_decode($data);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
