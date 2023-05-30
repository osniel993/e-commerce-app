<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $sku = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $categories = "";

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tags = "";

    #[ORM\Column]
    private ?int $quantity_stock = 0;

    #[ORM\Column]
    private ?int $quantity_sold = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = "";

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $more_info = "";

    #[ORM\Column(nullable: true)]
    private ?int $rating = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private string $attached_img = "";

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMoreInfo(): ?string
    {
        return $this->more_info;
    }

    public function setMoreInfo(?string $more_info): self
    {
        $this->more_info = $more_info;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getAttachedImg(): string
    {
        return $this->attached_img;
    }

    public function setAttachedImg(?string $attached_img): self
    {
        $this->attached_img = $attached_img;

        return $this;
    }

    public function getCategories(): ?string
    {
        return $this->categories;
    }

    public function setCategories(?string $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getQuantityStock(): ?int
    {
        return $this->quantity_stock;
    }

    public function setQuantityStock(int $quantity_stock): self
    {
        $this->quantity_stock = $quantity_stock;

        return $this;
    }

    public function getQuantitySold(): ?int
    {
        return $this->quantity_sold;
    }

    public function setQuantitySold(int $quantity_sold): self
    {
        $this->quantity_sold = $quantity_sold;

        return $this;
    }
}
