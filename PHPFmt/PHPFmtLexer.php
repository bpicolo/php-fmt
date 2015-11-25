<?php

namespace PhpFormat;

use \PhpParser\Lexer;
use \PhpParser\Parser\Tokens;

class PHPFmtLexer extends Lexer\Emulative {
    // Preserve raw token information in string literals so \n and newlines are
    // Differentiated

    public function __construct(array $options = array()) {
        parent::__construct($options);
    }

    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null) {
        $tokenId = parent::getNextToken($value, $startAttributes, $endAttributes);

        if ($tokenId == Tokens::T_CONSTANT_ENCAPSED_STRING
            || $tokenId == Tokens::T_LNUMBER
            || $tokenId == Tokens::T_DNUMBER
            || $tokenId == Tokens::T_ENCAPSED_AND_WHITESPACE
        ) {
            $endAttributes['originalValue'] = $value;
        }

        return $tokenId;
    }
}
