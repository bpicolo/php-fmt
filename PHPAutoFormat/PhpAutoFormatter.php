<?php

namespace PhpAutoFormat;

require_once 'PrettyPrinter.php';
require_once 'RawTokenLexer.php';

class PHPAutoFormatter {

    public function __construct() {
        $this->parser = new \PhpParser\Parser(new RawTokenLexer);
        $this->printer = new \PhpAutoFormat\PrettyPrinter();
    }

    public function parseCode($code) {
        try {
            $ast = $this->parser->parse($code);
            return $this->printer->prettyPrintFile($ast);
        } catch (\PhpParser\Error $e) {
            echo 'Parser Error: ', $e->getMessage();
        }
    }

    public function parseFile($filepath) {
        $code = file_get_contents($filepath);
        return $this->parseCode($code);
    }
}
