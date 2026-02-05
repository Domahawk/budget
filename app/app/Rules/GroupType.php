<?php

namespace App\Rules;

use App\Enums\GroupType as GroupTypeEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class GroupType implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $type = GroupTypeEnum::tryFrom($value);

        if ($type === null) {
            $fail("Group type can only be '{${GroupTypeEnum::PERSONAL->value}}' or '{${GroupTypeEnum::SHARED->value}}'.}'.");
        }
    }
}
