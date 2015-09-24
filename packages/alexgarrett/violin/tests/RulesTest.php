<?php

use Violin\Rules\IpRule;
use Violin\Rules\IntRule;
use Violin\Rules\UrlRule;
use Violin\Rules\MaxRule;
use Violin\Rules\MinRule;
use Violin\Rules\BoolRule;
use Violin\Rules\DateRule;
use Violin\Rules\RegexRule;
use Violin\Rules\AlnumRule;
use Violin\Rules\AlphaRule;
use Violin\Rules\EmailRule;
use Violin\Rules\ArrayRule;
use Violin\Rules\NumberRule;
use Violin\Rules\CheckedRule;
use Violin\Rules\BetweenRule;
use Violin\Rules\MatchesRule;
use Violin\Rules\RequiredRule;
use Violin\Rules\AlnumDashRule;

use Violin\Support\MessageBag;

class RulesTest extends PHPUnit_Framework_TestCase
{
    public function testBetweenRule()
    {
        $betweenRule = new BetweenRule;

        $this->assertFalse(
            $betweenRule->run('5', [], [10, 15])
        );

        $this->assertFalse(
            $betweenRule->run(5, [], [10, 15])
        );

        $this->assertTrue(
            $betweenRule->run('100', [], [100, 500])
        );

        $this->assertTrue(
            $betweenRule->run(499, [], [100, 500])
        );

        $this->assertTrue(
            $betweenRule->run('300', [], [100, 500])
        );
    }

    public function testIntRule()
    {
        $intRule = new IntRule;

        $this->assertFalse(
            $intRule->run('two', [], [])
        );

        $this->assertTrue(
            $intRule->run('2', [], [])
        );

        $this->assertTrue(
            $intRule->run(10, [], [])
        );
    }

    public function testNumberRule()
    {
        $numberRule = new NumberRule;

        $this->assertFalse(
            $numberRule->run('dale', [], [])
        );

        $this->assertFalse(
            $numberRule->run('', [], [])
        );

        $this->assertFalse(
            $numberRule->run('three', [], [])
        );

        $this->assertTrue(
            $numberRule->run('1', [], [])
        );

        $this->assertTrue(
            $numberRule->run('3.14159265359', [], [])
        );

        $this->assertTrue(
            $numberRule->run(3.14159265359, [], [])
        );
    }

    public function testMatchesRule()
    {
        $matchesRule = new MatchesRule;

        $this->assertFalse(
            $matchesRule->run('cats', [
                'password' => 'cats',
                'password_again' => 'catz',
            ], ['password_again'])
        );

        $this->assertTrue(
            $matchesRule->run('cats', [
                'password' => 'cats',
                'password_again' => 'cats',
            ], ['password_again'])
        );
    }

    public function testRequiredRule()
    {
        $requiredRule = new RequiredRule;

        $this->assertFalse(
            $requiredRule->run('', [], [])
        );

        $this->assertFalse(
            $requiredRule->run('   ', [], [])
        );

        $this->assertFalse(
            $requiredRule->run(' ', [], [])
        ); // Contains whitespace character

        $this->assertTrue(
            $requiredRule->run('cats', [], [])
        );

        $this->assertTrue(
            $requiredRule->run('  cats  ', [], [])
        );
    }

    public function testAlnumRule()
    {
        $alnumRule = new AlnumRule;

        $this->assertFalse(
            $alnumRule->run('cats_123', [], [])
        );

        $this->assertFalse(
            $alnumRule->run('cats-_123', [], [])
        );

        $this->assertFalse(
            $alnumRule->run('cats123!', [], [])
        );

        $this->assertTrue(
            $alnumRule->run('cats123', [], [])
        );

        $this->assertTrue(
            $alnumRule->run('cats', [], [])
        );
    }

    public function testAlnumDashRule()
    {
        $alnumDashRule = new AlnumDashRule;

        $this->assertFalse(
            $alnumDashRule->run('cats123!', [], [])
        );

        $this->assertFalse(
            $alnumDashRule->run('cats(123)', [], [])
        );

        $this->assertTrue(
            $alnumDashRule->run('cats_123', [], [])
        );

        $this->assertTrue(
            $alnumDashRule->run('i_love-cats', [], [])
        );

        $this->assertTrue(
            $alnumDashRule->run('cat__love', [], [])
        );

        $this->assertTrue(
            $alnumDashRule->run('cat--love', [], [])
        );
    }

    public function testAlphaRule()
    {
        $alphaRule = new AlphaRule;

        $this->assertFalse(
            $alphaRule->run('cats123', [], [])
        );

        $this->assertFalse(
            $alphaRule->run('cats!', [], [])
        );

        $this->assertFalse(
            $alphaRule->run('   cats   ', [], [])
        );

        $this->assertTrue(
            $alphaRule->run('cats', [], [])
        );
    }

    public function testArrayRule()
    {
        $arrayRule = new ArrayRule;

        $this->assertFalse(
            $arrayRule->run('not an array', [], [])
        );

        $this->assertFalse(
            $arrayRule->run("['not', 'an', 'array']", [], [])
        );

        $this->assertTrue(
            $arrayRule->run(['an', 'array'], [], [])
        );

        $this->assertTrue(
            $arrayRule->run([], [], [])
        );
    }

    public function testBoolRule()
    {
        $boolRule = new BoolRule;

        $this->assertFalse(
            $boolRule->run('false', [], [])
        );

        $this->assertFalse(
            $boolRule->run('true', [], [])
        );

        $this->assertFalse(
            $boolRule->run(1, [], [])
        );

        $this->assertFalse(
            $boolRule->run(0, [], [])
        );

        $this->assertTrue(
            $boolRule->run(true, [], [])
        );

        $this->assertTrue(
            $boolRule->run(false, [], [])
        );
    }

    public function testEmailRule()
    {
        $emailRule = new EmailRule;

        $this->assertFalse(
            $emailRule->run('ilove@', [], [])
        );

        $this->assertFalse(
            $emailRule->run('ilove@cats', [], [])
        );

        $this->assertTrue(
            $emailRule->run('ilove@cats.com', [], [])
        );
    }

    public function testIpRule()
    {
        $ipRule = new IpRule;

        $this->assertFalse(
            $ipRule->run('127', [], [])
        );

        $this->assertFalse(
            $ipRule->run('127.0.0', [], [])
        );

        $this->assertFalse(
            $ipRule->run('www.duckduckgo.com', [], [])
        );

        $this->assertTrue(
            $ipRule->run('0.0.0.0', [], [])
        );

        $this->assertTrue(
            $ipRule->run('127.0.0.1', [], [])
        );

        $this->assertTrue(
            $ipRule->run('FE80:0000:0000:0000:0202:B3FF:FE1E:8329', [], [])
        );

        $this->assertTrue(
            $ipRule->run('FE80::0202:B3FF:FE1E:8329', [], [])
        );

        $this->assertTrue(
            $ipRule->run('::1', [], [])
        );
    }

    public function testMaxRuleWithNumbers()
    {
        $maxRule = new MaxRule;

        $this->assertFalse(
            $maxRule->run('100', [], ['10', 'number'])
        );

        $this->assertFalse(
            $maxRule->run(100, [], ['99', 'number'])
        );

        $this->assertFalse(
            $maxRule->run(3.14, [], ['3.10', 'number'])
        );

        $this->assertTrue(
            $maxRule->run('50', [], ['100', 'number'])
        );

        $this->assertTrue(
            $maxRule->run(50, [], ['100', 'number'])
        );

        $this->assertTrue(
            $maxRule->run('5.5', [], ['100', 'number'])
        );
    }

    public function testMinRuleWithNumbers()
    {
        $minRule = new MinRule;

        $this->assertFalse(
            $minRule->run('10', [], ['100', 'number'])
        );

        $this->assertFalse(
            $minRule->run(99, [], ['100', 'number'])
        );

        $this->assertFalse(
            $minRule->run(3.10, [], ['3.14', 'number'])
        );

        $this->assertTrue(
            $minRule->run('100', [], ['50', 'number'])
        );

        $this->assertTrue(
            $minRule->run(100, [], ['50', 'number'])
        );

        $this->assertTrue(
            $minRule->run('100', [], ['5.5', 'number'])
        );
    }

    public function testMaxRuleWithStrings()
    {
        $maxRule = new MaxRule;

        $this->assertFalse(
            $maxRule->run('william', [], ['5'])
        );

        $this->assertFalse(
            $maxRule->run('100000', [], ['5'])
        );

        $this->assertTrue(
            $maxRule->run('billy', [], ['5'])
        );

        $this->assertTrue(
            $maxRule->run('100', [], ['5'])
        );

        $this->assertTrue(
            $maxRule->run('99999', [], ['5'])
        );
    }

    public function testMinRuleWithStrings()
    {
        $minRule = new MinRule;

        $this->assertFalse(
            $minRule->run('billy', [], ['10'])
        );

        $this->assertFalse(
            $minRule->run('5', [], ['10'])
        );

        $this->assertFalse(
            $minRule->run('10', [], ['10'])
        );

        $this->assertTrue(
            $minRule->run('william', [], ['5'])
        );

        $this->assertTrue(
            $minRule->run('99999', [], ['5'])
        );
    }

    public function testUrlRule()
    {
        $urlRule = new UrlRule;

        $this->assertFalse(
            $urlRule->run('www.com', [], [])
        );

        $this->assertFalse(
            $urlRule->run('duckduckgo.com', [], [])
        );

        $this->assertFalse(
            $urlRule->run('www.duckduckgo', [], [])
        );

        $this->assertFalse(
            $urlRule->run('127.0.0.1', [], [])
        );

        $this->assertTrue(
            $urlRule->run('http://www.duckduckgo.com', [], [])
        );

        $this->assertTrue(
            $urlRule->run('http://127.0.0.1', [], [])
        );

        $this->assertTrue(
            $urlRule->run('ftp://127.0.0.1', [], [])
        );

        $this->assertTrue(
            $urlRule->run('ssl://codecourse.com', [], [])
        );

        $this->assertTrue(
            $urlRule->run('ssl://127.0.0.1', [], [])
        );

        $this->assertTrue(
            $urlRule->run('http://codecourse.com', [], [])
        );
    }

    public function testDateRule()
    {
        $dateRule = new DateRule;

        $this->assertFalse(
            $dateRule->run('', [], [])
        );

        $this->assertFalse(
            $dateRule->run('0000-00-00', [], [])
        );

        $this->assertFalse(
            $dateRule->run('40th November 1989', [], [])
        );

        $this->assertTrue(
            $dateRule->run('16th November 1989', [], [])
        );

        $this->assertTrue(
            $dateRule->run('1989-11-16', [], [])
        );

        $this->assertTrue(
            $dateRule->run('16-11-1989', [], [])
        );

        $dateTime = new DateTime('2 days ago');

        $this->assertFalse(
            $dateRule->run($dateTime->format('x y z'), [], [])
        );

        $this->assertTrue(
            $dateRule->run($dateTime->format('d M Y'), [], [])
        );
    }

    public function testCheckedRule()
    {
        $checkedRule = new CheckedRule;

        $this->assertFalse(
            $checkedRule->run('', [], [])
        );

        $this->assertFalse(
            $checkedRule->run('   ', [], [])
        );

        $this->assertTrue(
            $checkedRule->run('on', [], [])
        );

        $this->assertTrue(
            $checkedRule->run('yes', [], [])
        );

        $this->assertTrue(
            $checkedRule->run(1, [], [])
        );

        $this->assertTrue(
            $checkedRule->run('1', [], [])
        );

        $this->assertTrue(
            $checkedRule->run(true, [], [])
        );

        $this->assertTrue(
            $checkedRule->run('true', [], [])
        );
    }

    public function testRegexRule()
    {
        $regexRule = new RegexRule;

        $exampleRegex = '/b[aeiou]g/';

        $this->assertFalse(
            $regexRule->run('banter', [], [$exampleRegex])
        );

        $this->assertTrue(
            $regexRule->run('bag', [], [$exampleRegex])
        );

        $this->assertTrue(
            $regexRule->run('big', [], [$exampleRegex])
        );
    }
}
