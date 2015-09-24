<?php

/**
 * Violin example. Checking and getting first error.
 * 
 * This checks a specific field has an error, and then outputs
 * the first error that occured for that field. This is most
 * likely what you'd use in a real life situation within
 * your HTML form.
 */

require '../../vendor/autoload.php';

use Violin\Violin;

$v = new Violin;

$v->validate([
    'username'  => ['dalegarrett1234567890', 'required|alpha|min(3)|max(20)'],
    'email'     => ['dale.codecourse.com', 'required|email']
]);

if ($v->errors()->has('email')) { // Check if any errors exist for 'email'.
    echo $v->errors()->first('email'); // First 'email' error (string).
}
