<?php

namespace PhpFormat;

require_once 'PrettyPrinter.php';
require_once 'PHPFmtLexer.php';

use PhpParser\ParserFactory;

class PHPFormatter {

    public function __construct() {
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP5, new \PhpFormat\PHPFmtLexer);
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
