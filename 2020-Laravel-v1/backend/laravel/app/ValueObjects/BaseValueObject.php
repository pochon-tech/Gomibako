<?php

namespace App\ValueObjects;

interface ValueObjects
{
    // 不変の性質を持たせるためにGetのみ
    public function get();
}