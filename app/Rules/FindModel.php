<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FindModel implements Rule
{
    public $model;

    public function __construct(Callable $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->model = ($this->finder)($value);
        return $this->model !== null;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Object not found';
    }
}
