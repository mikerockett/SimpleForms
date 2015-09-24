<?php

/**
 * Violin example. Custom rule.
 * 
 * Creating a custom rule using the addRule method, passing in a
 * closure which should return false if the check has failed,
 * or true if the check has passed.
 */

require '../vendor/autoload.php';

use Violin\Violin;

$v = new Violin;

$v->addRuleMessage('startsWithNumber', 'The {field} must start with a number.');

$v->addRule('startsWithNumber', function($value, $input, $args) {
    $value = trim($value);
    return is_numeric($value[0]);
});

$v->validate([
    'username'  => ['dale', 'required|min(3)|max(20)|startsWithNumber']
]);

if ($v->passes()) {
    // Passed
} else {
    var_dump($v->errors()->all());
}
