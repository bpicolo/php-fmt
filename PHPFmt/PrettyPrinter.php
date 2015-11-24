<?php

namespace PhpFormat;

use \PhpParser\Node\Expr;
use \PhpParser\Node\Scalar;
use \PhpParser\Node\Stmt;

class PrettyPrinter extends \PhpParser\PrettyPrinter\Standard {
    /*
    Modified https://github.com/tcopestake/PHP-Parser-PSR-2-pretty-printer
    */
    protected $maxLineLength = 80;
    protected $methodIndents = 0;
    protected $indents = 0;
    protected $totalIndents = 0;

    public function addIndent() {
        $this->indents++;
        $this->totalIndents++;
    }

    public function subIndent() {
        $this->indents--;
        $this->totalIndents--;
    }

    public function indentedStmt($stmts) {
        $this->addIndent();
        $out = $this->pStmts($stmts);
        $this->subIndent();
        return $out;
    }

    public function pParam(\PhpParser\Node\Param $node) {
        return ($node->type ? $this->pType($node->type) . ' ' : '')
             . ($node->byRef ? '&' : '')
             . ($node->variadic ? '...' : '')
             . '$' . $node->name
             . ($node->default ? ' = ' . $this->p($node->default) : '');
    }

    public function pArg(\PhpParser\Node\Arg $node) {
        return ($node->byRef ? '&' : '') . ($node->unpack ? '...' : '') . $this->p($node->value);
    }

    protected function implementsSeparated($nodes) {
        if (count($nodes) > 1) {
            return "\n    ".$this->pImplode($nodes, ",\n    ");
        } else {
            return ' '.$this->pImplode($nodes, ', ');
        }
    }

    public function pExpr_Closure(\PhpParser\Node\Expr\Closure $node) {
        return ($node->static ? 'static ' : '')
             . 'function ' . ($node->byRef ? '&' : '')
             . '(' . $this->pCommaSeparated($node->params) . ')'
             . (!empty($node->uses) ? ' use (' . $this->pCommaSeparated($node->uses) . ')': '')
             . ' {' . $this->indentedStmt($node->stmts) . "\n" . '}';
    }

    public function pStmt_Function(Stmt\Function_ $node) {
        $result = 'function ' . ($node->byRef ? '&' : '') . $node->name
            . '(' . $this->pCommaSeparated($node->params) . ')'
            . (null !== $node->returnType ? ' : ' . $this->pType($node->returnType) : '')
            . ' {';

        if ((
            $this->shouldNewlineBreakArguments($result) &&
            $node->params
        )) {
            $method_params = $this->pCommaSeparated($node->params, true);
            $result = 'function ' . ($node->byRef ? '&' : '') . $node->name
                . '(' . $this->pCommaSeparated($node->params) . ')'
                . (null !== $node->returnType ? ' : ' . $this->pType($node->returnType) : '')
                . ' {';
        }

        if ($node->stmts) {
            $result .= $this->indentedStmt($node->stmts);
            $result .= "\n";
        }
        $result .= '}'."\n";

        return $result;
    }

    public function pStmt_Class(\PhpParser\Node\Stmt\Class_ $node) {
        $result = $this->pModifiers($node->type)
             . 'class ' . $node->name
             . (null !== $node->extends ? ' extends ' . $this->p($node->extends) : '')
             . (!empty($node->implements) ? ' implements' . $this->implementsSeparated($node->implements) : '')
             . ' {';

        if ($node->stmts) {
            $result .= $this->indentedStmt($node->stmts);
            $result .= "\n";
        }
        $result .= '}'."\n";

        return $result;
    }

    public function shouldNewlineBreakArguments($text) {
        $length = (($this->totalIndents * 4) + (strlen($text)));
        return $length > $this->maxLineLength;
    }

    public function pStmt_ClassMethod(\PhpParser\Node\Stmt\ClassMethod $node) {
        $method_params = $this->pCommaSeparated($node->params);

        $defaultFirstLine = $this->pModifiers($node->type)
             . 'function ' . ($node->byRef ? '&' : '') . $node->name
             . '(' . $method_params . ')';

        if ((
            $this->shouldNewlineBreakArguments($defaultFirstLine) &&
            $node->params
        )) {
            $method_params = $this->pCommaSeparated($node->params, true);
        }

        $result = $this->pModifiers($node->type)
            . 'function ' . ($node->byRef ? '&' : '') . $node->name
            . '(' . $method_params
            . ')';


        if (null !== $node->stmts) {
            $result .= ' {';
            $result .= $this->indentedStmt($node->stmts);
            $result .= "\n" .'}' . "\n";
        } else {
            $result .= ';';
        }

        return $result;
    }

    public function pExpr_MethodCall(\PhpParser\Node\Expr\MethodCall $node) {
        $method_name = $this->pObjectProperty($node->name);
        $method_params = $this->pCommaSeparated($node->args);

        $line = $this->pVarOrNewExpr($node->var) . '->' . $method_name
             . '(' . $method_params . ')';

        if (($this->shouldNewlineBreakArguments($line) && $node->args)) {
            $method_params = $this->pCommaSeparated($node->args, true);
            $line = $this->pVarOrNewExpr($node->var) . '->' . $method_name
                . '('
                . $method_params
                . ')';
        }

        return $line;
    }

    public function pExpr_FuncCall(\PhpParser\Node\Expr\FuncCall $node) {
        $formatted = $this->p($node->name) . '(' . $this->pCommaSeparated($node->args) . ')';

        if (($this->shouldNewlineBreakArguments($formatted) && $node->args)) {
            $this->addIndent();
            $formatted = $this->p($node->name) . '('
                . $this->pCommaSeparated($node->args, true)
                . ')';
            $this->subIndent();
        }
        return $formatted;
    }

    // Scalars

    public function pScalar_String(Scalar\String_ $node) {
        $hasNewlines = strpos($node->value, "\n") !== false;
        $quoteChar = '\'';
        $value = $node->value;
        if ($hasNewlines) {
            $quoteChar = '"';
            // change newlines to newline literals
            $value = addcslashes($value, "\n\r\t\f\v$" . '"' . "\\");
        } else {
            $value = addcslashes($value, '\'');
        }
        // Keep newline literals (actual \n) but remove newlines in string
        return "$quoteChar" . $value . "$quoteChar";
    }

    public function pScalar_Encapsed(Scalar\Encapsed $node) {
        return '"' . $this->pEncapsList($node->parts, '"') . '"';
    }

    // Control flow

    public function pStmt_If(Stmt\If_ $node) {
        return 'if (' . $this->p($node->cond) . ') {'
             . $this->indentedStmt($node->stmts) . "\n" . '}'
             . $this->pImplode($node->elseifs)
             . (null !== $node->else ? $this->p($node->else) : '')
             . (($node->elseifs !== null || $node->else !== null) ? '': "\n");
    }

    public function pStmt_ElseIf(Stmt\ElseIf_ $node) {
        return ' elseif (' . $this->p($node->cond) . ') {'
             . $this->indentedStmt($node->stmts) . "\n" . '}';
    }

    public function pStmt_Else(Stmt\Else_ $node) {
        return ' else {' . $this->indentedStmt($node->stmts) . "\n" . '}';
    }

    public function pStmt_For(Stmt\For_ $node) {
        return 'for ('
             . $this->pCommaSeparated($node->init) . ';' . (!empty($node->cond) ? ' ' : '')
             . $this->pCommaSeparated($node->cond) . ';' . (!empty($node->loop) ? ' ' : '')
             . $this->pCommaSeparated($node->loop)
             . ') {' . $this->indentedStmt($node->stmts) . "\n" . '}' . "\n";
    }

    public function pStmt_Foreach(Stmt\Foreach_ $node) {
        return 'foreach (' . $this->p($node->expr) . ' as '
             . (null !== $node->keyVar ? $this->p($node->keyVar) . ' => ' : '')
             . ($node->byRef ? '&' : '') . $this->p($node->valueVar) . ') {'
             . $this->indentedStmt($node->stmts) . "\n" . '}' . "\n";
    }

    public function pStmt_While(Stmt\While_ $node) {
        return 'while (' . $this->p($node->cond) . ') {'
             . $this->indentedStmt($node->stmts) . "\n" . '}' . "\n";
    }

    public function pStmt_Do(Stmt\Do_ $node) {
        return 'do {' . $this->indentedStmt($node->stmts) . "\n"
             . '} while (' . $this->p($node->cond) . ');';
    }

    public function pStmt_Catch(Stmt\Catch_ $node) {
        $out = ' catch (' . $this->p($node->type) . ' $' . $node->var . ') {';
        $out .= $this->indentedStmt($node->stmts) . "\n" . '}' . "\n";

        return $out;
    }

    public function pStmt_Switch(Stmt\Switch_ $node) {
        // TODO line-breaking for length?
        return 'switch (' . $this->p($node->cond) . ') {'
             . $this->indentedStmt($node->cases) . "\n" . '}';
    }

    public function pStmt_Case(Stmt\Case_ $node) {
        return (null !== $node->cond ? 'case ' . $this->p($node->cond) : 'default') . ':'
             . $this->indentedStmt($node->stmts);
    }

    public function pStmt_TryCatch(Stmt\TryCatch $node) {
        $out = 'try {';
        $out .= $this->indentedStmt($node->stmts);
        $out .= "\n" . '}' . $this->pImplode($node->catches);

        if ($node->finallyStmts !== null) {
            $out .= ' finally {';
            $out .= $this->indentedStmt($node->finallyStmts) . "\n" . '}' . "\n";
        }

        return $out;
    }

    protected function pStmts(array $nodes, $indent = true) {
        if (!$nodes) {
            return '';
        }
        $startIndent = $this->indents;
        $this->indents = 0;

        $formattedNodes = [];
        foreach ($nodes as $node) {
            array_push(
                $formattedNodes,
                (
                    $this->pComments($node->getAttribute('comments', array()))
                        . $this->p($node)
                        . ($node instanceof Expr ? ';' : '')
                )
            );
        }

        foreach ($formattedNodes as &$node) {
            // TODO is there a way to kill rtrim? Not sure.
            $node = rtrim(str_repeat('    ', $startIndent).implode(
                "\n".str_repeat('    ', $startIndent),
                explode("\n", $node)
            ), " \t\r\0\x0b");
        }
        $this->indents = $startIndent;

        return "\n".implode("\n", $formattedNodes);

    }

    // Other

    public function pExpr_List(Expr\List_ $node) {
        $pList = array();
        foreach ($node->vars as $var) {
            if (null === $var) {
                $pList[] = '';
            } else {
                $pList[] = $this->p($var);
            }
        }

        return 'list(' . implode(', ', $pList) . ')';
    }

    public function pExpr_Array(Expr\Array_ $node) {
        $out = '[' . $this->pCommaSeparated($node->items) . ']';
        if ($this->shouldNewlineBreakArguments($out)) {
            $out = '[' . $this->pCommaSeparated($node->items, true) . ']';
        }
        return $out;
    }

    public function pStmt_InlineHTML(Stmt\InlineHTML $node) {
        return '?>' . "\n" . $node->value . '<?php ';
    }

    /**
     * Pretty prints a node, maintaining indentation
     *
     * @param Node $node Node to be pretty printed
     *
     * @return string Pretty printed node
     */
    protected function p(\PhpParser\Node $node) {
        return $this->{'p' . $node->getType()}($node);
    }

    /**
     * Pretty prints an array of nodes and implodes the printed values with commas.
     *
     * @param Node[] $nodes Array of Nodes to be printed
     *
     * @return string Comma separated pretty printed nodes
     */
    protected function pCommaSeparated(array $nodes, $breakIndent = false) {
        // We expect to be in call or array e.g. (1,2,3)
        // Our job is to take the inner bits, split them by new lines
        // And indent them one further from our starting level of indentation
        if ($breakIndent) {
            $startIndent = $this->indents;
            $this->indents = 0;
            $out = $this->pImplode($nodes, ",\n");
            // For every line in the output, indent by a single space
            $out = "\n    ".implode("\n    ", explode("\n", $out));
            $this->indents = $startIndent;
            return $out."\n";
        } else {
            return $this->pImplode($nodes, ', ');
        }
    }

    // Newlines at end of file, also remove their indent nonsense
    public function prettyPrint(array $stmts) {
        $this->preprocessNodes($stmts);
        return ltrim($this->pStmts($stmts, false));
    }

    /**
     * Pretty prints an expression.
     *
     * @param Expr $node Expression node
     *
     * @return string Pretty printed node
     */
    public function prettyPrintExpr(Expr $node) {
        return  $this->p($node);
    }

    // No op this thing
    protected function pNoIndent($string) {
        return $string;
    }
}
