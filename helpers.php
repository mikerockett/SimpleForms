<?php

/**
 * SimpleForms for ProcessWire
 * A simple form processor. Uses AJAX, configurable with JSON. Front-end is up to you.
 *
 * Helpers
 */

/**
 * Dump & Die (debug helper)
 * @param  mixed   $mixed
 * @param  boolean $die
 * @return void
 */
function dd($mixed, $die = true)
{
    header('Content-Type: text/plain');
    var_dump($mixed);
    $die && die;
}

/**
 * truePath, courtesy Christian at Stack Overflow
 * @see http://stackoverflow.com/a/4050444/1626250
 * @param  string $path
 * @return string
 */
function truePath($path)
{
    $unipath = strlen($path) == 0 || $path{0} != '/';
    if (strpos($path, ':') === false && $unipath) {
        $path = getcwd() . DIRECTORY_SEPARATOR . $path;
    }
    $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
    $absolutes = array();
    foreach ($parts as $part) {
        if ('.' == $part) {
            continue;
        }
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = $part;
        }
    }
    $path = implode(DIRECTORY_SEPARATOR, $absolutes);
    if (file_exists($path) && linkinfo($path) > 0) {
        $path = readlink($path);
    }
    $path = !$unipath ? '/' . $path : $path;

    return $path;
}
