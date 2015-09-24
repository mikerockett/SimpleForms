<?php

/**
 * Violin example. Message placeholders.
 * 
 * Shows the placeholders you can use when defining messages. You can
 * output the name of the field, the value given by the user, and
 * the arguments that were passed into the rule.
 */

require '../vendor/autoload.php';

use Violin\Violin;

$v = new Violin;

$v->addRuleMessage('between', 'The {field} must be between {$0} and {$1}, you gave {value}');

$v->validate([
    'age'  => ['82', 'required|between(18, 35)']
]);

if ($v->passes()) {
    // Passed
} else {
    var_dump($v->errors()->all());
}
