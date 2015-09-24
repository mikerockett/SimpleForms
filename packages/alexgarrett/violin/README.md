# violin

[![Build Status](https://travis-ci.org/alexgarrett/violin.svg?branch=master)](https://travis-ci.org/alexgarrett/violin)

Violin is an easy to use, highly customisable PHP validator.

**Note: This package is under heavy development and is not recommended for production.**

## Installing

Install using Composer.

```json
{
    "require": {
        "alexgarrett/violin": "2.*"
    }
}
```

## Basic usage

```php
use Violin\Violin;

$v = new Violin;

$v->validate([
    'name'  => ['billy', 'required'],
    'age'   => [20, 'required|int']
]);

if($v->passes()) {
    echo 'Validation passed, woo!';
} else {
    echo '<pre>', var_dump($v->errors()->all()), '</pre>';
}
```

## Adding custom rules

Adding custom rules is simple. If the closure returns false, the rule fails.

```php
$v->addRuleMessage('isbanana', 'The {field} field expects "banana", found "{value}" instead.');

$v->addRule('isbanana', function($value, $input, $args) {
    return $value === 'banana';
});

$v->validate([
    'fruit' => ['apple', 'isbanana']
]);
```

## Adding custom error messages

You can add rule messages, or field messages for total flexibility.

### Adding a rule message

```php
$v->addRuleMessage('required', 'You better fill in the {field} field, or else.');
```

### Adding rule messages in bulk

```php
$v->addRuleMessages([
    'required' => 'You better fill in the {field} field, or else.',
    'int'      => 'The {field} needs to be an integer, but I found {value}.',
]);
```

### Adding a field message

Any field messages you add are used before any default or custom rule messages.

```php
$v->addFieldMessage('username', 'required', 'You need to enter a username to sign up.');
```

### Adding field messages in bulk

```php
$v->addFieldMessages([
    'username' => [
        'required' => 'You need to enter a username to sign up.'
    ],
    'age' => [
        'required' => 'I need your age.',
        'int'      => 'Your age needs to be an integer.',
    ]
]);
```

### Using Field Aliases

Field Aliases helps you format any error messages without showing weird form names or the need to create a custom error.

```php
$v->validate([
    'username_box|Username' => ['' => 'required']
]);

// Error output: "Username is required."
```
### Extending Violin

You can extend the Violin class to add custom rules, rule messages and field messages. This way, you can keep a tidy class to handle custom validation if you have any dependencies, like a database connection or language files.

```php
class MyValidator extends Violin
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;

        // Add rule message for custom rule method.
        $this->addRuleMessage('uniqueUsername', 'That username is taken.');
    }

    // Custom rule method for checking a unique username in our database.
    // Just prepend custom rules with validate_
    public function validate_uniqueUsername($value, $input, $args)
    {
        $user = $this->db->prepare("
            SELECT count(*) as count
            FROM users
            WHERE username = :username
        ");

        $user->execute(['username' => $value]);

        if($user->fetchObject()->count) {
            return false; // Username exists, so return false.
        }

        return true;
    }
}

// A database connection.
$db = new PDO('mysql:host=127.0.0.1;dbname=website', 'root', 'root');

// Instantiate your custom class with dependencies.
$v = new MyValidator($db);

$v->validate([
    'username' => ['billy', 'required|uniqueUsername']
]);
```

## Rules

This list of rules are **in progress**. Of course, you can always contribute to the project if you'd like to add more to the base ruleset.

#### alnum

If the value is alphanumeric.

#### alnumDash

If the value is alphanumeric. Dashes and underscores are permitted.

#### alpha

If the value is alphabetic letters only.

#### alphaDash

If the value is alphabetic letters only. Dashes and underscores are permitted.

#### array

If the value is an array.

#### between(int, int)

Checks if the value is within the intervals defined. This check is inclusive, so 5 is between 5 and 10.

#### bool

If the value is a boolean.

#### email

If the value is a valid email.

#### int

If the value is an integer, including numbers within strings. 1 and '1' are both classed as integers.

#### number

If the value is a number, including numbers within strings.

> Numeric strings consist of optional sign, any number of digits, optional decimal part and optional exponential part. Thus +0123.45e6 is a valid numeric value. Hexadecimal (e.g. 0xf4c3b00c), Binary (e.g. 0b10100111001), Octal (e.g. 0777) notation is allowed too but only without sign, decimal and exponential part.

#### ip

If the value is a valid IP address.

#### min(int, [number])

Check if string length is greater than or equal to given `int`. To check the size of a number, pass the optional `number` option.

```php
$v->validate([
    'username' => ['billy', 'required|min(3)|max(20)'],
    'age' => ['20', 'required|min(18, number)|max(100, number)']
]);
```

#### max(int, [number])

Check if string length is less than or equal to given `int`. To check the size of a number, pass the optional `number` option.

#### required

If the value is present.

#### url

If the value is formatted as a valid URL.

#### matches(field)

Checks if one given input matches the other. For example, checking if *password* matches *password_confirm*.

#### date

If the given input is a valid date.

You can validate human readable dates like '25th October 1961' and instances of `DateTime`. For example:

```php
$twoDaysAgo = new DateTime('2 days ago');
$date = $twoDaysAgo->format('d M Y');

$v->validate([
    'date' => [$date, 'required|date']
]);
```

#### checked

If a field has been 'checked' or not, meaning it contains one of the following values: *'yes'*, *'on'*, *'1'*, *1*, *true*, or *'true'*. This can be used for determining if an HTML checkbox has been checked.

#### regex(expression)

If the given input has a match for the regular expression given.

## Contributing

Please file issues under GitHub, or submit a pull request if you'd like to directly contribute.

### Running tests

Tests are run with phpunit. Run `./vendor/bin/phpunit` to run tests.
