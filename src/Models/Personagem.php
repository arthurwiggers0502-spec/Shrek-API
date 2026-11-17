
<?php

declare(strict_types=1);

namespace ShrekApi\Models;

class Personagem
{
    private ?int $id;
    private string $nome;
    private string $especie;
    private ?float $alturaCm;
    private ?string $genero;
    private ?string $descricao;
    private ?string $origem;

    public function __construct(
        string $nome,
        string $especie,
        ?float $alturaCm = null,
        ?string $genero = null,
        ?string $descricao = null,
        ?string $origem = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->especie = $especie;
        $this->alturaCm = $alturaCm;
        $this->genero = $genero;
        $this->descricao = $descricao;
        $this->origem = $origem;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getEspecie(): string
    {
        return $this->especie;
    }

    public function getAlturaCm(): ?float
    {
        return $this->alturaCm;
    }

    public function getGenero(): ?string
    {
        return $this->genero;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function getOrigem(): ?string
    {
        return $this->origem;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'especie' => $this->especie,
            'altura_cm' => $this->alturaCm,
            'genero' => $this->genero,
            'descricao' => $this->descricao,
            'origem' => $this->origem
        ];
    }
}
