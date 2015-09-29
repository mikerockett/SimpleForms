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

/**
 * Convert numbers to words.
 * @see http://www.karlrixon.co.uk/writing/convert-numbers-to-words-with-php/
 * @copyright Karl Rixon
 * @param  [type] $number [description]
 * @return [type]         [description]
 */
function toWords($number)
{

    $hyphen = '-';
    $conjunction = ' and ';
    $separator = ', ';
    $negative = 'negative ';
    $decimal = ' point ';
    $dictionary = array(
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'fourty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
        100 => 'hundred',
        1000 => 'thousand',
        1000000 => 'million',
        1000000000 => 'billion',
        1000000000000 => 'trillion',
        1000000000000000 => 'quadrillion',
        1000000000000000000 => 'quintillion',
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'toWords only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . toWords(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens = ((int) ($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . toWords($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = toWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= toWords($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

/**
 * String template parser.
 * @return string
 */
function plate()
{
    $args = func_get_args();
    $input = $args[0];
    $ucfirstMethod = 'ucfirst';

    // Loop through each argument, checking for replacements.
    for ($i = 0; $i < func_num_args(); $i++) {
        $formatter = "%\\{($ucfirstMethod\\:)?$i\\}%im";
        $pluralise = "%\\[([a-z]+)\\|([a-z]+):($i)\\]%im";
        $arg = $args[$i + 1];

        // If matched, get matches.
        $matched = preg_match($formatter, $input, $matches);

        // ucfirst if necessary.
        if ($matched && strpos($matches[0], $ucfirstMethod) >= 0) {
            $arg = ucfirst($arg);
        }

        // Replace the input
        if ($matched) {
            $input = preg_replace($formatter, $arg, $input);
        }

        // Check for plurals/singulars
        if (preg_match($pluralise, $input)) {
            $input = preg_replace($pluralise, ($arg === 1 || strtolower($arg) === 'one') ? "$1" : "$2", $input);
        }
    }

    return $input;
}
