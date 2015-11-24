<?php

namespace PhpAutoFormat;

require_once 'PhpAutoFormatter.php';

function get_doc() {
    return "
    php-auto-format

    Usage:
        bin/php-auto-format.php <filepath> [--inplace]

    Options:
        -h --help       Show this screen.
        -i --inplace    Rewrite the file in place.
    ";
}

class PhpAutoFormatterClient {

    public function __construct() {
        $this->formatter = new PHPAutoFormatter();
    }

    public function run() {
        $params = array(
            'help'=>true,
            'version'=>'php-auto-format 0.1'
        );
        $args = \Docopt::handle(get_doc(), $params);

        if (isset($args['<filepath>'])) {
            $filepath = $args['<filepath>'];
            if (!file_exists($filepath)) {
                die("File $filepath does not exist\n");
            }
            $out = $this->formatter->parseFile($filepath);
            if ($args['--inplace']) {
                $handler = fopen($filepath, 'w+');
                fwrite($handler, $out);
                fclose($handler);
            } else {
                echo $out;
            }
        }

    }
}
