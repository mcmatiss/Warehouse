<?php
namespace App;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class Product
{
    private string $id;
    private string $name;
    private Carbon $createdAt;
    private ?Carbon $updatedAt;
    private int $units;

    public function __construct(
        string $name,
        int $units,
        Uuid $id = null,
        Carbon $createdAt = null,
        ?Carbon $updatedAt = null
    )
    {
        $this->id = $id ? self::getId() : Uuid::uuid4();
        $this->name = $name;
        $this->createdAt = $createdAt ? new Carbon($createdAt) : Carbon::now('UTC');
        $this->updatedAt = $updatedAt ? new Carbon($updatedAt) : null;
        $this->units = $units;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt->setTimezone('Europe/Riga');
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt ? $this->updatedAt->setTimezone('Europe/Riga') : null;
    }

    public function update(): void
    {
        $this->updatedAt = Carbon::now('UTC');
    }

    public function getUnits(): int
    {
        return $this->units;
    }

    public function setUnits(int $units): void
    {
        $this->units = $units;
    }

    public function withdrawUnits(int $amount): void
    {
        $this->units -= $amount;
    }

}