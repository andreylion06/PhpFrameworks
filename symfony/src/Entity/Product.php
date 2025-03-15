<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "products")]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private float $price;

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function getPrice(): float { return $this->price; }
    public function setPrice(float $price): void { $this->price = $price; }
}
