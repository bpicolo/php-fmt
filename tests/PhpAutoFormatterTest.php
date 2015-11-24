<?php

require_once __DIR__ . '/../PHPAutoFormat/PhpAutoFormatter.php';

use PhpAutoFormat\PHPAutoFormatter;

function endswith($string, $test) {
    $strlen = strlen($string);
    $testlen = strlen($test);
    if ($testlen > $strlen) {
        return false;
    }
    return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
}

class PhpAutoFormatterTest extends PHPUnit_Framework_TestCase
{
    /* Uses a lot of cases modified from PHP_CodeSniffer */
    public function setUp() {
        $this->fmt = new PHPAutoFormatter();
    }

    public function testAllCases() {
        $TEST_CASE_DIR = realpath(__DIR__ .'/cases');
        echo "Directory is ". $TEST_CASE_DIR;
        $dir = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($TEST_CASE_DIR)
        );

        foreach ($dir as $name => $object) {
            if (!endswith($name, '.txt')) {
                continue;
            }

            //$this->assertEquals($this->fmt->parseFile($name), file_get_contents($name . '.fixed'));
            $lines1 = explode("\n", $this->fmt->parseFile($name));
            $lines2 = explode("\n", file_get_contents($name . '.fixed'));

            for ($l = 0; $l < count($lines1); $l++) {
                if (!($lines1[$l] == $lines2[$l])) {
                    $first = $lines1[$l];
                    $second = $lines2[$l];
                    $this->assertTrue(
                        false,
                        "Line $l in file $name is incorrect\n"
                        . "expected: $second\n"
                        . "actual: $first"
                    );
                }
                $this->assertEquals($lines1[$l], $lines2[$l]);
            }
        }
    }
}
