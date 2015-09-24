<?php

/**
 * Violin example. Basic validation.
 * 
 * This shows basic validation, passing in an array to the
 * validate method and checking if the validation passes
 * with the passes method. Then dumps errors if failed.
 */

require '../vendor/autoload.php';

use Violin\Violin;

$v = new Violin;

$v->validate([
    'username'  => ['dale', 'required|alpha|min(3)|max(20)'],
    'email'     => ['dale@codecourse.com', 'required|email'],
    'password'  => ['ilovecats', 'required'],
    'password_confirm' => ['ilovecats', 'required|matches(password)']
]);

if ($v->passes()) {
    // Passed
} else {
    var_dump($v->errors()->all());
}
