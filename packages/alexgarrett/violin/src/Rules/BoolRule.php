<?php

namespace Violin\Rules;

use Violin\Contracts\RuleContract;

class BoolRule implements RuleContract
{
    public function run($value, $input, $args)
    {
        return is_bool($value);
    }

    public function error()
    {
        return '{field} must be a boolean.';
    }

    public function canSkip()
    {
        return true;
    }
}
