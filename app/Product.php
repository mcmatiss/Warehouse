<?php

namespace App;

use Carbon\Carbon;
use JsonSerializable;

class Product implements JsonSerializable
{
    private string $name;
    private int $units;
    private ?string $id;
    private Carbon $createdAt;
    private ?Carbon $updatedAt;

    public function __construct(
        string  $name,
        int     $units,
        ?string $id = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    )
    {
        $this->name = $name;
        $this->units = $units;
        $this->id = $id ?: $this->generateUuid();
        $this->createdAt = $createdAt ? Carbon::parse($createdAt) : Carbon::now('UTC');
        $this->updatedAt = $updatedAt ? Carbon::parse($updatedAt) : null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;

        $this->update();
    }

    public function getUnits(): int
    {
        return $this->units;
    }

    public function setUnits(int $units): void
    {
        $this->units = $units;

        $this->update();
    }

    public function withdrawUnits(int $amount): void
    {
        $this->units -= $amount;

        $this->update();
    }

    public function getId(): string
    {
        return $this->id;
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

    public function generateUuid(): string
    {
        return json_decode(
            file_get_contents(
                'https://www.uuidtools.com/api/generate/v4'
            )
        )[0];
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'units' => $this->units,
            'id' => $this->id,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
}
