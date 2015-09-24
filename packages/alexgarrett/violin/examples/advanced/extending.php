<?php

/**
 * Violin example. Extending.
 * 
 * You can extend the Violin class to create your own validator,
 * making it easier to keep everything in one place, and more
 * importantly, the ability to pass dependencies into the
 * class.
 *
 * This example checks a specfic value isn't already taken
 * within a table in your database.
 */

require '../../vendor/autoload.php';

//-- Validator.php

use Violin\Violin;

class Validator extends Violin
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;

        $this->addRuleMessage('unique', 'That {field} is already taken.');
    }

    /**
     * Check if a value already exists in a database table.
     * 
     * @param  mixed $value
     * @param  array $input
     * @param  array $args
     * 
     * @return bool
     */
    public function validate_unique($value, $input, $args)
    {
        $table  = $args[0];
        $column = $args[1];
        $value  = trim($value);

        $exists = $this->db->prepare("
            SELECT count(*) as count
            FROM {$table}
            WHERE {$column} = :value
        ");

        $exists->execute([
            'value' => $value
        ]);

        return ! (bool) $exists->fetchObject()->count;
    }
}

//-- Any other file

// Some database dependency
$db = new PDO('mysql:dbname=project;host=localhost', 'root', 'root');

$v = new Validator($db);

$v->validate([
    'username'  => ['alex', 'required|alpha|min(3)|max(20)|unique(users, username)'],
    'email'     => ['alex@codecourse.com', 'required|email|unique(users, email)']
]);

if ($v->passes()) {
    // Passed
} else {
    var_dump($v->errors()->all());
}
