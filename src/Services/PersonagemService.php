<?php
declare(strict_types=1);

namespace ShrekApi\Services;

use InvalidArgumentException;
use ShrekApi\Repositories\PersonagemRepository;

class PersonagemService
{
    private PersonagemRepository $repository;

    public function __construct()
    {
        $this->repository = new PersonagemRepository();
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
        return $this->repository->criar(
            $this->validar($dados)
        );
    }

    public function atualizar(int $id, array $dados): array|false
    {
        $atual = $this->repository->buscarPorId($id);

        if (!$atual) {
            return false;
        }

        $dadosCompletos = array_merge($atual, $dados);
        $dadosValidados = $this->validar($dadosCompletos);

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
        $nome = trim((string) ($dados['nome'] ?? ''));
        $especie = trim((string) ($dados['especie'] ?? ''));

        if ($nome === '') {
            throw new InvalidArgumentException('O nome é obrigatório.');
        }

        if ($especie === '') {
            throw new InvalidArgumentException('A espécie é obrigatória.');
        }

        $altura = $dados['altura_cm'] ?? null;

        if ($altura === '') {
            $altura = null;
        }

        if ($altura !== null && (!is_numeric($altura) || (float) $altura <= 0)) {
            throw new InvalidArgumentException('A altura deve ser um número maior que zero.');
        }

        $confiabilidade = $dados['confiabilidade_altura'] ?? null;

        if ($confiabilidade === '') {
            $confiabilidade = null;
        }

        if (
            $confiabilidade !== null &&
            !in_array($confiabilidade, ['oficial', 'estimada', 'desconhecida'], true)
        ) {
            throw new InvalidArgumentException('Confiabilidade da altura inválida.');
        }

        $camposTexto = [
            'genero',
            'descricao',
            'origem',
            'fonte_altura'
        ];

        $resultado = [
            'nome' => $nome,
            'especie' => $especie,
            'altura_cm' => $altura,
            'confiabilidade_altura' => $confiabilidade
        ];

        foreach ($camposTexto as $campo) {
            $valor = $dados[$campo] ?? null;

            if ($valor !== null && !is_string($valor)) {
                throw new InvalidArgumentException("O campo {$campo} deve ser texto.");
            }

            $resultado[$campo] = $valor;
        }

        return $resultado;
    }
}