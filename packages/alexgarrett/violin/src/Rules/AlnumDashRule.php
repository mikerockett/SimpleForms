<?php

namespace Violin\Rules;

use Violin\Contracts\RuleContract;

class AlnumDashRule implements RuleContract
{
    public function run($value, $input, $args)
    {
        return (bool) preg_match('/^[\pL\pM\pN_-]+$/u', $value);
    }

    public function error()
    {
        return '{field} must be alphanumeric with dashes and underscores permitted.';
    }

    public function canSkip()
    {
        return true;
    }
}
