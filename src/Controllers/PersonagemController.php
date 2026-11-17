<?php
declare(strict_types=1);

namespace ShrekApi\Controllers;

use InvalidArgumentException;
use JsonException;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ShrekApi\Services\PersonagemService;

class PersonagemController
{
    private PersonagemService $service;

    public function __construct()
    {
        $this->service = new PersonagemService();
    }

    private function json(Response $response, array $dados, int $status = 200): Response
    {
        $response->getBody()->write(
            json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    private function lerJson(Request $request): array
    {
        try {
            $dados = json_decode(
                (string) $request->getBody(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $e) {
            throw new InvalidArgumentException('O JSON enviado é inválido.');
        }

        if (!is_array($dados) || array_is_list($dados)) {
            throw new InvalidArgumentException('Envie um objeto JSON válido.');
        }

        return $dados;
    }

    private function erro(Response $response, callable $operacao): Response
    {
        try {
            return $operacao();
        } catch (InvalidArgumentException $e) {
            return $this->json($response, ['erro' => $e->getMessage()], 400);
        } catch (PDOException $e) {
            $status = in_array($e->getCode(), ['23505', '23503'], true)
                ? 409
                : 500;

            return $this->json($response, [
                'erro' => $status === 409
                    ? 'Conflito com um registro existente ou relacionado.'
                    : 'Erro interno ao acessar o banco de dados.'
            ], $status);
        } catch (\Throwable $e) {
            return $this->json($response, [
                'erro' => 'Erro interno do servidor.'
            ], 500);
        }
    }

    public function listar(Request $request, Response $response): Response
    {
        return $this->erro($response, function () use ($response) {
            return $this->json($response, [
                'personagens' => $this->service->listar()
            ]);
        });
    }

    public function buscar(Request $request, Response $response, array $args): Response
    {
        return $this->erro($response, function () use ($response, $args) {
            $id = filter_var($args['id'] ?? null, FILTER_VALIDATE_INT);

            if (!$id || $id < 1) {
                return $this->json($response, ['erro' => 'ID inválido.'], 400);
            }

            $personagem = $this->service->buscar($id);

            if (!$personagem) {
                return $this->json($response, ['erro' => 'Personagem não encontrado.'], 404);
            }

            return $this->json($response, ['personagem' => $personagem]);
        });
    }

    public function criar(Request $request, Response $response): Response
    {
        return $this->erro($response, function () use ($request, $response) {
            $dados = $this->lerJson($request);
            $id = $this->service->criar($dados);
            $personagem = $this->service->buscar($id);

            return $this->json($response, [
                'mensagem' => 'Personagem cadastrado com sucesso.',
                'personagem' => $personagem
            ], 201);
        });
    }

    public function atualizar(Request $request, Response $response, array $args): Response
    {
        return $this->erro($response, function () use ($request, $response, $args) {
            $id = filter_var($args['id'] ?? null, FILTER_VALIDATE_INT);

            if (!$id || $id < 1) {
                return $this->json($response, ['erro' => 'ID inválido.'], 400);
            }

            $dados = $this->lerJson($request);
            $personagem = $this->service->atualizar($id, $dados);

            if (!$personagem) {
                return $this->json($response, ['erro' => 'Personagem não encontrado.'], 404);
            }

            return $this->json($response, [
                'mensagem' => 'Personagem atualizado com sucesso.',
                'personagem' => $personagem
            ]);
        });
    }

    public function excluir(Request $request, Response $response, array $args): Response
    {
        return $this->erro($response, function () use ($response, $args) {
            $id = filter_var($args['id'] ?? null, FILTER_VALIDATE_INT);

            if (!$id || $id < 1) {
                return $this->json($response, ['erro' => 'ID inválido.'], 400);
            }

            if (!$this->service->excluir($id)) {
                return $this->json($response, ['erro' => 'Personagem não encontrado.'], 404);
            }

            return $this->json($response, [
                'mensagem' => 'Personagem excluído com sucesso.'
            ]);
        });
    }
}