<?php
declare(strict_types=1);

namespace ShrekApi\Services;

use InvalidArgumentException;
use ShrekApi\Repositories\FilmeRepository;

class FilmeService
{
    private FilmeRepository $repository;

    public function __construct()
    {
        $this->repository = new FilmeRepository();
    }

    public function listar(): array
    {
        return $this->repository->listar();
    }

    public function buscar(int $id): array|false
    {
        return $this->repository->buscarPorId($id);
    }

    public function criar(array $dados): int
    {
        return $this->repository->criar($this->validar($dados));
    }

    public function atualizar(int $id, array $dados): array|false
    {
        $atual = $this->repository->buscarPorId($id);

        if (!$atual) {
            return false;
        }

        $dados = array_merge($atual, $dados);
        $dadosValidados = $this->validar($dados);

        $this->repository->atualizar($id, $dadosValidados);

        return $this->repository->buscarPorId($id);
    }

    public function excluir(int $id): bool
    {
        if (!$this->repository->buscarPorId($id)) {
            return false;
        }

        return $this->repository->excluir($id);
    }

    private function validar(array $dados): array
    {
        $titulo = trim((string) ($dados['titulo'] ?? ''));

        if ($titulo === '') {
            throw new InvalidArgumentException(
                'O título é obrigatório.'
            );
        }

        $ano = filter_var(
            $dados['ano_lancamento'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($ano === false || $ano === null || $ano < 1900 || $ano > 2100) {
            throw new InvalidArgumentException(
                'Ano de lançamento inválido.'
            );
        }

        $duracao = $dados['duracao_minutos'] ?? null;

        if ($duracao === '') {
            $duracao = null;
        }

        if ($duracao !== null) {
            $duracao = filter_var($duracao, FILTER_VALIDATE_INT);

            if ($duracao === false || $duracao <= 0) {
                throw new InvalidArgumentException(
                    'A duração deve ser um inteiro maior que zero.'
                );
            }
        }

        $sinopse = $dados['sinopse'] ?? null;

        if ($sinopse !== null && !is_string($sinopse)) {
            throw new InvalidArgumentException(
                'A sinopse deve ser um texto.'
            );
        }

        return [
            'titulo' => $titulo,
            'ano_lancamento' => $ano,
            'duracao_minutos' => $duracao,
            'sinopse' => $sinopse
        ];
    }
}
