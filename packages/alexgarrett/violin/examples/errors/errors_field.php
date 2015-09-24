<?php

/**
 * Violin example. Errors for a specific field.
 * 
 * Simply getting a list of errors for a specific field that
 * occured while trying to validate the data passed in
 * against the rules given.
 */

require '../../vendor/autoload.php';

use Violin\Violin;

$v = new Violin;

$v->validate([
    'username'  => ['dalegarrett1234567890', 'required|alpha|min(3)|max(20)'],
    'email'     => ['dale.codecourse.com', 'required|email']
]);

var_dump($v->errors()->get('username')); // Array of all 'username' errors.
