<?php declare(strict_types=1);

class Estacao
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Método para buscar todas as estações
    public function getAll(): array|bool
    {
        try {
            $stmt = $this->pdo->query("SELECT id, endereco, latitude, longitude, api_token, created_at, updated_at FROM estacoes");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar estações: " . $e->getMessage());
        }
    }

    // Método para buscar uma estação específica pelo ID
    public function getById(int $id): array|bool
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id, endereco, latitude, longitude, api_token, created_at, updated_at FROM estacoes WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar estação: " . $e->getMessage());
        }
    }

    // Método para criar uma nova estação
    public function create(array $data): int
    {
        try {
            $token = $this->generateToken(); // Gerar o token
            $stmt = $this->pdo->prepare("
                INSERT INTO estacoes (endereco, latitude, longitude, api_token)
                VALUES (:endereco, :latitude, :longitude, :api_token)
            ");
            $stmt->execute([
                ':endereco' => $data['endereco'],
                ':latitude' => $data['latitude'],
                ':longitude' => $data['longitude'],
                ':api_token' => $token
            ]);
            return (int)$this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erro ao criar estação: " . $e->getMessage());
        }
    }

    // Método para atualizar uma estação
    public function update(int $id, array $data): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE estacoes
                SET endereco = :endereco, latitude = :latitude, longitude = :longitude, updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");
            return $stmt->execute([
                ':endereco' => $data['endereco'],
                ':latitude' => $data['latitude'],
                ':longitude' => $data['longitude'],
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar estação: " . $e->getMessage());
        }
    }

    // Método para deletar uma estação
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM estacoes WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Erro ao deletar estação: " . $e->getMessage());
        }
    }

    // Método para gerar um token seguro (32 caracteres)
    private function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    // Método para buscar uma estação pelo token
    public function getByToken(string $token): array|bool
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id, endereco, latitude, longitude, api_token, created_at, updated_at FROM estacoes WHERE api_token = ?");
            $stmt->execute([$token]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar estação pelo token: " . $e->getMessage());
        }
    }
}
