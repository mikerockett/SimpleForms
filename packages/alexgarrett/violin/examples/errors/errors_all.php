<?php

/**
 * Violin example. All errors.
 * 
 * Simply getting a list of errors that occured while trying
 * to validate the data passed in against the rules given.
 */

require '../../vendor/autoload.php';

use Violin\Violin;

$v = new Violin;

$v->validate([
    'username'  => ['dalegarrett1234567890', 'required|alpha|min(3)|max(20)'],
    'email'     => ['dale.codecourse.com', 'required|email']
]);

var_dump($v->errors()->all()); // Array of all errors.
