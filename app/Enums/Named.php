<?php

namespace App\Enums;

use BackedEnum;
use InvalidArgumentException;

trait Named
{
    public abstract function getName(): string;
    public static function keyToName(array $except = []): array
    {
        if (is_subclass_of(self::class, BackedEnum::class)) {
            $result = [];
            foreach (self::cases() as $case) {
                if (in_array($case, $except)) continue;
                $result[$case->value] = $case->getName();
            }
            return $result;
        }
        throw new InvalidArgumentException("Class does not implement " . BackedEnum::class);
    }
}
