<?php
namespace App;

use Carbon\Carbon;

class Warehouse implements \JsonSerializable
{
    private array $products = [];
    //public function __construct(array $products)
    //{}
    public function products(): array
    {
        return $this->products;
    }
    public function addProduct(Product $product): void
    {
        $this->products[] = $product;
    }
    public function removeProduct(int $index): void
    {
        array_splice($this->products, $index, 1);
    }
    public function jsonSerialize()
    {
        return json_encode(serialize($this->products), JSON_PRETTY_PRINT);
    }
}