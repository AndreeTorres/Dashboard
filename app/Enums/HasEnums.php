<?php

namespace App\Enums;

use App\Attributes\Description;
use Illuminate\Support\Str;

trait HasEnums
{
    public static function keys(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toSelectOptions(): array
    {
        return array_combine(
            self::keys(),
            array_map(fn ($key) => self::from($key)->translatedLabel(), self::keys())
        );
    }

    public static function labels(): array
    {
        $labels = [];
        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->label();
        }

        return $labels;
    }

    public static function translatedLabels(): array
    {
        return array_map(fn ($label) => __($label), self::labels());
    }

    public function label(): string
    {
        $reflected = new \ReflectionEnumUnitCase(self::class, $this->name);
        $attributes = $reflected->getAttributes(Description::class);

        return $attributes[0]->newInstance()->text ?? $this->value;
    }

    public function translatedLabel(bool $plural = false): string
    {
        if ($plural) {
            return __(Str::plural($this->label()));
        }

        return __($this->label());
    }

    public static function getRandom(): self
    {
        return self::cases()[rand(0, count(self::cases()) - 1)];
    }
}
