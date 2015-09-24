<?php

/**
 * Violin example. Basic validation, two arrays.
 * 
 * This shows basic validation using two arrays, one for data,
 * and one for the ruleset. This is useful if you'd like to
 * extract the rules to a seperate class or variable.
 */

require '../vendor/autoload.php';

use Violin\Violin;

$v = new Violin;

$data = [
    'username'          => 'dale',
    'email'             => 'dale@codecourse.com',
    'password'          => 'ilovecats',
    'password_confirm'  => 'ilovecats'
];

$rules = [
    'username'          => 'required|alpha|min(3)|max(20)',
    'email'             => 'required|email',
    'password'          => 'required',
    'password_confirm'  => 'required|matches(password)'
];

$v->validate($data, $rules);

if ($v->passes()) {
    // Passed
} else {
    var_dump($v->errors()->all());
}
