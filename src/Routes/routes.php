<?php
declare(strict_types=1);

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ShrekApi\Controllers\PersonagemController;
use ShrekApi\Database\Connection;
use ShrekApi\Controllers\FilmeController;

return function (App $app): void {

    $filmeController = new FilmeController();

    $app->get('/api/v1/filmes', [$filmeController, 'listar']);
    $app->get('/api/v1/filmes/{id}', [$filmeController, 'buscar']);
    $app->post('/api/v1/filmes', [$filmeController, 'criar']);
    $app->put('/api/v1/filmes/{id}', [$filmeController, 'atualizar']);
    $app->delete('/api/v1/filmes/{id}', [$filmeController, 'excluir']);

    $app->get('/api/v1/status', function (Request $request, Response $response) {
        $dados = [
            'api' => 'Shrek API',
            'versao' => '1.0.0',
            'status' => 'online'
        ];

        $response->getBody()->write(
            json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/api/v1/database/status', function (Request $request, Response $response) {
        try {
            $pdo = Connection::connect();
            $resultado = $pdo->query('SELECT NOW() AS horario')->fetch();

            $dados = [
                'database' => 'PostgreSQL',
                'status' => 'connected',
                'horario_servidor' => $resultado['horario']
            ];

            $response->getBody()->write(json_encode($dados, JSON_PRETTY_PRINT));

            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode([
                'erro' => 'Falha na conexão com o banco de dados'
            ]));

            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
        }
    });

    $controller = new PersonagemController();

    $app->get('/api/v1/personagens', [$controller, 'listar']);
    $app->get('/api/v1/personagens/{id}', [$controller, 'buscar']);
    $app->post('/api/v1/personagens', [$controller, 'criar']);
    $app->put('/api/v1/personagens/{id}', [$controller, 'atualizar']);
    $app->delete('/api/v1/personagens/{id}', [$controller, 'excluir']);
};