<?php
declare(strict_types=1);

namespace ShrekApi\Repositories;

use PDO;
use ShrekApi\Database\Connection;

class PersonagemRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::connect();
    }

    public function listar(): array
    {
        $sql = "SELECT * FROM personagens ORDER BY id";
        return $this->db->query($sql)->fetchAll();
    }

    public function buscarPorId(int $id): array|false
    {
        $sql = "SELECT * FROM personagens WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    public function criar(array $dados): int
    {
        $sql = "INSERT INTO personagens
            (nome, especie, altura_cm, genero, descricao, origem,
             fonte_altura, confiabilidade_altura)
            VALUES
            (:nome, :especie, :altura_cm, :genero, :descricao, :origem,
             :fonte_altura, :confiabilidade_altura)
            RETURNING id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($dados);

        return (int) $stmt->fetchColumn();
    }

    public function atualizar(int $id, array $dados): bool
    {
        $sql = "UPDATE personagens SET
            nome = :nome,
            especie = :especie,
            altura_cm = :altura_cm,
            genero = :genero,
            descricao = :descricao,
            origem = :origem,
            fonte_altura = :fonte_altura,
            confiabilidade_altura = :confiabilidade_altura
            WHERE id = :id";

        $dados['id'] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($dados);
    }

    public function excluir(int $id): bool
    {
        $sql = "DELETE FROM personagens WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}