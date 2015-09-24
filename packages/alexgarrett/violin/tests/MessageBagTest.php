<?php

use Violin\Support\MessageBag;

class MessageBagTest extends PHPUnit_Framework_TestCase
{
    protected $mb;

    protected $messages = [
        'name' => [
            'You must fill in the name field.',
            'Your name must only be letters.',
        ],
        'username' => [
            'Your username is required.',
            'Your username must be alphanumeric (with dashes and underscores permitted)'
        ],
        'password' => [
            'Your password must be greater than 6 characters.'
        ]
    ];
    
    public function setUp()
    {
        $this->mb = new MessageBag($this->messages);
    }

    public function testMessageBagContainsCorrectKeys()
    {
        $keys = $this->mb->keys();

        $this->assertContains('name', $keys);
        $this->assertContains('username', $keys);
        $this->assertContains('password', $keys);
    }

    public function testFirstErrorForFieldExists()
    {
        $this->assertEquals(
            $this->mb->first('name'),
            $this->messages['name'][0]
        );
    }

    public function testAllErrorsForFieldExist()
    {
        $this->assertEquals(
            $this->mb->get('name'),
            $this->messages['name']
        );
    }

    public function testAllErrorsExist()
    {
        $flatMessages = iterator_to_array(new RecursiveIteratorIterator(
            new RecursiveArrayIterator($this->messages)
        ), false);

        $this->assertEquals($flatMessages, $this->mb->all());
    }

    public function testMessageBagHasError()
    {
        $this->assertTrue($this->mb->has('name'));
        $this->assertTrue($this->mb->has('username'));
        $this->assertTrue($this->mb->has('password'));
    }
}
