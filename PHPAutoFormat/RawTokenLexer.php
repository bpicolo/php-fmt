<?php

namespace PhpAutoFormat;

use \PhpParser\Lexer;
use \PhpParser\Parser;

class RawTokenLexer extends Lexer {
    // Preserve raw token information in string literals so \n and newlines are
    // Differentiated

    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null) {
        $tokenId = parent::getNextToken($value, $startAttributes, $endAttributes);

        if ($tokenId == Parser::T_CONSTANT_ENCAPSED_STRING
            || $tokenId == Parser::T_LNUMBER
            || $tokenId == Parser::T_DNUMBER
        ) {
            $endAttributes['originalValue'] = $value;
        }

        return $tokenId;
    }
}
