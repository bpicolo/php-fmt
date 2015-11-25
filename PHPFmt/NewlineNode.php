<?php

namespace PhpFormat;

use PhpParser\NodeAbstract;


class NewlineNode extends NodeAbstract
{
    public function __construct($value, array $attributes = array()) {
        parent::__construct($attributes);
    }

    public function getSubNodeNames() {
        return array();
    }

    public function getType() {
        return 'Newline';
    }
}
