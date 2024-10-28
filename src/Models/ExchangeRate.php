<?php

namespace App\Models;

class ExchangeRate
{
    public function __construct(
        private int $Cur_ID,
        private string $Cur_Abbreviation,
        private string $Cur_Name,
        private float $Cur_Scale,
        private float $Cur_OfficialRate,
    ) {

    }

    public function Cur_ID(): int
    {
        return $this->Cur_ID;
    }

    public function Cur_Abbreviation(): string
    {
        return $this->Cur_Abbreviation;
    }

    public function Cur_Name(): string
    {
        return $this->Cur_Name;
    }

    public function Cur_Scale(): float
    {
        return $this->Cur_Scale;
    }

    public function Cur_OfficialRate(): float
    {
        return $this->Cur_OfficialRate;
    }
}
