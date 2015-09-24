<?php

/**
 * Violin example. Custom rule.
 * 
 * Creating a custom rule using the addRule method, passing in a
 * closure which should return false if the check has failed,
 * or true if the check has passed.
 *
 * This example shows the use of arguments that can be used
 * to make rules that require arguments.
 */

require '../vendor/autoload.php';

use Violin\Violin;

$v = new Violin;

$v->addRuleMessage('startsWith', 'The {field} must start with "{$0}".');

$v->addRule('startsWith', function($value, $input, $args) {
    $value = trim($value);
    return $value[0] === $args[0];
});

$v->validate([
    'username'  => ['dale', 'required|min(3)|max(20)|startsWith(a)']
]);

if ($v->passes()) {
    // Passed
} else {
    var_dump($v->errors()->all());
}
