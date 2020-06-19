<?php

namespace App\ValueObjects;

interface BaseValueObject
{
    // 不変の性質を持たせるためにGetのみ
    public function get();
}