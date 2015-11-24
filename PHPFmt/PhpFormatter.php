<?php

namespace PhpFormat;

require_once 'PrettyPrinter.php';
require_once 'PHPFmtLexer.php';

class PHPFormatter {

    public function __construct() {
        $this->parser = new \PhpParser\Parser(new PHPFmtLexer);
        $this->printer = new \PhpFormat\PrettyPrinter();
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
