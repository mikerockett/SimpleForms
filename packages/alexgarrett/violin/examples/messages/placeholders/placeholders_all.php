<?php

/**
 * Violin example. Message placeholders.
 * 
 * Shows how you can output all arguments as a comma seperated list,
 * if you have a variable amount of arguments for a rule.
 */

require '../../../vendor/autoload.php';

use Violin\Violin;

$v = new Violin;

$v->addRuleMessage('isoneof', '{field} must be one of {$*}');

$v->addRule('isoneof', function($value, $input, $args) {
    $value = trim($value);
    return in_array($value, $args);
});

$v->validate([
    'age'  => ['sheep', 'required|isoneof(apples, pears)']
]);

if ($v->passes()) {
    // Passed
} else {
    var_dump($v->errors()->all());
}
