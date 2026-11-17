<?php
declare(strict_types=1);

namespace ShrekApi\Repositories;

use PDO;
use ShrekApi\Database\Connection;

class FilmeRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::connect();
    }

    public function listar(): array
    {
        return $this->db->query(
            "SELECT * FROM filmes ORDER BY id"
        )->fetchAll();
    }

    public function buscarPorId(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM filmes WHERE id = :id"
        );

        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    public function criar(array $dados): int
    {
        $sql = "INSERT INTO filmes
            (titulo, ano_lancamento, duracao_minutos, sinopse)
            VALUES
            (:titulo, :ano_lancamento, :duracao_minutos, :sinopse)
            RETURNING id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($dados);

        return (int) $stmt->fetchColumn();
    }

    public function atualizar(int $id, array $dados): bool
    {
        $sql = "UPDATE filmes SET
            titulo = :titulo,
            ano_lancamento = :ano_lancamento,
            duracao_minutos = :duracao_minutos,
            sinopse = :sinopse
            WHERE id = :id";

        $dados['id'] = $id;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($dados);

        return $stmt->rowCount() > 0;
    }

    public function excluir(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM filmes WHERE id = :id"
        );

        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
